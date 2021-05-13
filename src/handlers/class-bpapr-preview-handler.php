<?php
/**
 * Ajax Preview Handler.
 *
 * @package    BuddyPress Activity Plus Reloaded
 * @subpackage Handlers
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Handles ajax preview.
 */
class BPAPR_Preview_Handler {

	/**
	 * Boot the handler.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup hooks callback for ajax request
	 */
	public function setup() {
		add_action( 'wp_ajax_bpfb_preview_video', array( $this, 'ajax_preview_video' ) );
		add_action( 'wp_ajax_bpfb_preview_link', array( $this, 'ajax_preview_link' ) );
		add_action( 'wp_ajax_bpfb_preview_photo', array( $this, 'ajax_preview_photo' ) );
		add_action( 'wp_ajax_bpfb_preview_remote_image', array( $this, 'ajax_preview_remote_image' ) );
		add_action( 'wp_ajax_bpfb_remove_temp_images', array( $this, 'ajax_remove_temp_images' ) );

		do_action( 'bpfb_add_ajax_hooks' );
	}


	/**
	 * Handles video preview requests.
	 */
	public function ajax_preview_video() {
		$url      = ! empty( $_POST['data'] ) ? esc_url( $_POST['data'] ) : false;
		$url      = preg_match( '/^https?:\/\//i', $url ) ? $url : BPFB_PROTOCOL . $url;
		$warning  = __( 'There has been an error processing your request', 'bp-activity-plus-reloaded' );
		$response = $url ? __( 'Processing...', 'bp-activity-plus-reloaded' ) : $warning;
		$ret      = wp_oembed_get( $url );
		echo $ret ? $ret : $warning;
		exit();
	}


	/**
	 * Handles link preview requests.
	 */
	public function ajax_preview_link() {
		$url      = ! empty( $_POST['data'] ) ? esc_url( $_POST['data'] ) : '';
		$warning  = __( 'There has been an error processing your request', 'bp-activity-plus-reloaded' );
		$response = $url ? __( 'Processing...', 'bp-activity-plus-reloaded' ) : $warning;

		$images = array();
		$title  = $warning;
		$text   = $warning;

		if ( ! $url ) {
			header( 'Content-type: application/json' );
			echo json_encode(
				array(
					'url'    => $url,
					'images' => $images,
					'title'  => $title,
					'text'   => $text,
				)
			);
			exit( 0 );
		}

		$scheme = parse_url( $url, PHP_URL_SCHEME );
		if ( ! $scheme || ! preg_match( '/^https?$/', $scheme ) ) {
			$url = "http://{$url}";
		}

		$page = $this->get_remote_contents( $url );

		if ( ! function_exists( 'str_get_html' ) ) {
			require_once( BPFB_PLUGIN_BASE_DIR . '/lib/external/simple_html_dom.php' );
		}

		$html = str_get_html( $page );

		if ( ! $html ) {
			header( 'Content-type: application/json' );
			echo json_encode(
				array(
					'url'    => $url,
					'images' => $images,
					'title'  => $title,
					'text'   => $text,
				)
			);
			exit( 0 );
		}

		$str  = $html->find( 'text' );

		if ( empty( $str ) ) {
			header( 'Content-type: application/json' );
			echo json_encode(
				array(
					'url'    => '',
					'images' => $images,
					'title'  => esc_attr( $title ),
					'text'   => esc_attr( $text ),
				)
			);
			exit();
		}

		$image_els = $html->find( 'img' );
		foreach ( $image_els as $el ) {
			// Disregard spacers.
			if ( $el->width > 100 && $el->height > 1 ) {
				$images[] = esc_url( $el->src );
			}
		}

		$og_image = $html->find( 'meta[property=og:image]', 0 );

		if ( $og_image ) {
			array_unshift( $images, esc_url( $og_image->content ) );
		}

		$title = $html->find( 'title', 0 );
		$title = $title ? $title->plaintext : $url;

		$meta_description = $html->find( 'meta[name=description]', 0 );
		$og_description   = $html->find( 'meta[property=og:description]', 0 );

		$first_paragraph = $html->find( 'p', 0 );

		if ( $og_description && $og_description->content ) {
			$text = $og_description->content;
		} else if ( $meta_description && $meta_description->content ) {
			$text = $meta_description->content;
		} else if ( $first_paragraph && $first_paragraph->plaintext ) {
			$text = $first_paragraph->plaintext;
		} else {
			$text = $title;
		}

			$images = array_filter( $images );

		header( 'Content-type: application/json' );
		echo json_encode(
			array(
				'url'    => $url,
				'images' => $images,
				'title'  => esc_attr( $title ),
				'text'   => esc_attr( $text ),
			)
		);
		exit();
	}

	/**
	 * Handles image preview requests.
	 * Relies on ./lib/external/file_uploader.php for images upload handling.
	 * Stores images in the temporary storage.
	 */
	public function ajax_preview_photo() {
		if ( ! class_exists( 'qqFileUploader' ) ) {
			require_once( BPFB_PLUGIN_BASE_DIR . '/lib/external/file_uploader.php' );
		}
		$uploader = new qqFileUploader( bpapr_get_supported_image_extensions() );
		$result   = $uploader->handleUpload( BPFB_TEMP_IMAGE_DIR );
		echo htmlspecialchars( json_encode( $result ), ENT_NOQUOTES );
		exit();
	}

	/**
	 * Handles remote images preview
	 */
	public function ajax_preview_remote_image() {
		header( 'Content-type: application/json' );

		if ( ! empty( $_POST['data'] ) ) {
			$data = wp_unslash( $_POST['data'] );
		} else {
			$data = false;
		}

		if ( $data && is_array( $data ) ) {
			$data = array_map( 'esc_url', $data );
		}

		echo json_encode( $data );
		exit();
	}

	/**
	 * Clears up the temporary images storage.
	 */
	public function ajax_remove_temp_images() {
		header( 'Content-type: application/json' );
		$data = wp_parse_args(
			$_POST['data'],
			array(
				'bpfb_photos' => array(),
			)
		);

		foreach ( $data['bpfb_photos'] as $file ) {
			$path = bpapr_get_resolved_temp_path( $file );
			if ( ! empty( $path ) ) {
				@unlink( $path );
			}
		}

		echo json_encode( array( 'status' => 'ok' ) );
		exit();
	}

	/**
	 * Get the contents from remote url.
	 *
	 * @param string $url Remote URL.
	 *
	 * @return mixed Remote page as string, or (bool)false on failure
	 */
	private function get_remote_contents( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'user-agent' => __( 'BuddyPress Activity Plus', 'bp-activity-plus-reloaded' ), // Some sites will block default WP UA.
			)
		);
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$status = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $status ) {
			return false;
		}

		return $response['body'];
	}
}