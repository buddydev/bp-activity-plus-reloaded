<?php
/**
 * Core functions.
 *
 * @package    BuddyPress Activity Plus Reloaded
 * @subpackage Shortcodes
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode Generator & Processor.
 */
class BPAPR_Shortcodes {

	/**
	 * Registers shotcode processing procedures.
	 */
	public static function register() {
		$self = new self();
		// Register shortcodes.
		add_shortcode( 'bpfb_link', array( $self, 'process_link_tag' ) );
		add_shortcode( 'bpfb_video', array( $self, 'process_video_tag' ) );
		add_shortcode( 'bpfb_images', array( $self, 'process_images_tag' ) );

		// A fix for Ray's "oEmbed for BuddyPress" and similar plugins.
		add_filter( 'bp_get_activity_content_body', array( $self, 'do_shortcode' ), 1 );
		// RSS feed processing.
		add_filter( 'bp_get_activity_feed_item_description', 'do_shortcode' );
	}

	/**
	 * Generate shortcode for embedding links.
	 *
	 * Relies on ./forms/link_tag_template.php for markup rendering.
	 *
	 * @param array  $atts attributes.
	 * @param string $body content.
	 *
	 * @return string
	 */
	public function process_link_tag( $atts, $body ) {
		extract(
			shortcode_atts(
				array(
					'url'   => false,
					'title' => false,
					'image' => false,
				),
				$atts
			)
		);

		if ( empty( $url ) ) {
			return '';
		}

		$template = locate_template( array( 'link_tag_template.php' ) );
		if ( empty( $template ) ) {
			$template = BPFB_PLUGIN_BASE_DIR . '/lib/forms/link_tag_template.php';
		}

		ob_start();
		@include $template;
		$out = ob_get_clean();

		return $out;
	}

	/**
	 * Process video shortcode to generate proper markup..
	 *
	 * Relies on `wp_oembed_get()` for markup rendering.
	 *
	 * @param array  $atts attributes.
	 * @param string $content content.
	 *
	 * @return string
	 */
	public function process_video_tag( $atts, $content ) {
		return wp_oembed_get( $content, array( 'width' => BPAPR_Data::get( 'oembed_width', 450 ) ) );
	}

	/**
	 * Generate image markup from the image shortcode.
	 *
	 * Relies on ./forms/images_tag_template.php for markup rendering.
	 *
	 * @param array  $atts attributes.
	 * @param string $content content.
	 *
	 * @return string
	 */
	public function process_images_tag( $atts, $content ) {
		global $blog_id;
		$activity_blog_id = $blog_id;
		$images = self::extract_images( $content );
		$activity_id = bp_get_activity_id();
		$use_thickbox     = defined( 'BPFB_USE_THICKBOX' ) ? esc_attr( BPFB_USE_THICKBOX ) : 'thickbox';

		if ( $activity_id ) {
			$activity_blog_id = bp_activity_get_meta( $activity_id, 'bpfb_blog_id' );
		}

		$template = locate_template( array( 'images_tag_template.php' ) );

		if ( empty( $template ) ) {
			$template = BPFB_PLUGIN_BASE_DIR . '/lib/forms/images_tag_template.php';
		}

		ob_start();
		@include $template;
		$out = ob_get_clean();

		return $out;
	}

	/**
	 * Generate shortcode for embedding link.
	 *
	 * @param string $url url.
	 * @param string $title title.
	 * @param string $body body.
	 * @param string $image image.
	 *
	 * @return string
	 */
	public function create_link_tag( $url, $title, $body = '', $image = '' ) {
		if ( ! $url ) {
			return '';
		}
		$title = $this->_escape_shortcode( $title );
		$body  = ! empty( $body ) ? $this->_escape_shortcode( $body ) : $title;
		$title = esc_attr( $title );
		$image = esc_url( $image );
		$url   = esc_url( $url );

		return "[bpfb_link url='{$url}' title='{$title}' image='{$image}']{$body}[/bpfb_link]";
	}

	/**
	 * Creates the proper shortcode tag based on the submitted data.
	 */

	/**
	 * Generate video shortcode for embedding.
	 *
	 * @param string $url video url.
	 *
	 * @return string
	 */
	public function create_video_tag( $url ) {
		if ( ! $url ) {
			return '';
		}

		$url = preg_match( '/^https?:\/\//i', $url ) ? $url : BPFB_PROTOCOL . $url;
		$url = esc_url( $url );

		return "[bpfb_video]{$url}[/bpfb_video]";
	}


	/**
	 * Creates the proper shortcode tag based on the submitted data.
	 */
	/**
	 * Generate shortcode for embedding images.
	 *
	 * @param array $imgs images.
	 *
	 * @return string
	 */
	public function create_images_tag( $imgs ) {
		if ( ! $imgs ) {
			return '';
		}

		if ( ! is_array( $imgs ) ) {
			$imgs = (array) $imgs;
		}

		return "[bpfb_images]\n" . join( "\n", $imgs ) . "\n[/bpfb_images]";
	}

	/**
	 * Process our chortcodes inside activity content.
	 *
	 * @param string $content Content to check for shortcode and process accordingly.
	 *
	 * @return string
	 */
	public function do_shortcode( $content = '' ) {
		if ( false === strpos( $content, '[bpfb_' ) ) {
			return $content;
		}

		// Drop this because we'll be doing this right now.
		remove_filter( 'bp_get_activity_content_body', 'stripslashes_deep', 5 );
		// and process immediately, before allowing shortcode processing.
		$content = stripslashes_deep( $content );

		return do_shortcode( $content );
	}

	/**
	 * Checks whether we have an images list shortcode in content.
	 *
	 * @param string $content String to check.
	 *
	 * @return boolean
	 */
	public static function has_images( $content ) {
		return has_shortcode( $content, 'bpfb_images' );
	}

	/**
	 * Extracts images from shortcode content.
	 *
	 * @param string $shortcode_content Shortcode contents.
	 *
	 * @return array
	 */
	public static function extract_images( $shortcode_content ) {
		return explode( "\n", trim( strip_tags( $shortcode_content ) ) );
	}

	/**
	 * Escape shortcode-breaking characters.
	 *
	 * @param string $string String to process.
	 *
	 * @return string
	 */
	private function _escape_shortcode( $string = '' ) {
		if ( empty( $string ) ) {
			return $string;
		}

		$string = preg_replace( '/' . preg_quote( '[', '/' ) . '/', '&#91;', $string );
		$string = preg_replace( '/' . preg_quote( ']', '/' ) . '/', '&#93;', $string );

		return $string;
	}

}