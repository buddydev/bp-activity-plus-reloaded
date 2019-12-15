<?php
/**
 * Activity Update Handler.
 *
 * @package    BuddyPress Activity Plus Reloaded
 * @subpackage Handlers
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Activity Update Handler.
 */
class BPAPR_Activity_Update_Handler {

	/**
	 * Boot the handler.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	public function setup() {
		add_action( 'wp_ajax_bpfb_update_activity_contents', array( $this, 'ajax_update_activity_contents' ) );
	}

	/**
	 * This is where we actually save the activity update.
	 */
	public function ajax_update_activity_contents() {
		$activity    = '';
		$activity_id = 0;

		$bpfb_code = $this->get_embed_shortcode( isset( $_POST['data'] ) ? $_POST['data'] : array() );

		$bpfb_code = apply_filters( 'bpfb_code_before_save', $bpfb_code );

		// All done creating tags. Now, save the code.
		$group_id = ! empty( $_POST['group_id'] ) && is_numeric( $_POST['group_id'] ) ? (int) $_POST['group_id'] : false;

		if ( $bpfb_code ) {
			$content = ! empty( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : '';
			$content .= "\n{$bpfb_code}";
			$content = apply_filters( 'bp_activity_post_update_content', $content );

			if ( $group_id ) {
				$activity_id = groups_post_update(
					array(
						'content'  => $content,
						'group_id' => $group_id,
					)
				);
			} else {
				$activity_id = bp_activity_post_update( array( 'content' => $content ) );
			}

			bp_activity_update_meta( $activity_id, 'bpfb_blog_id', get_current_blog_id() );
		}

		if ( $activity_id ) {
			ob_start();
			if ( bp_has_activities( 'include=' . $activity_id ) ) {
				while ( bp_activities() ) {
					bp_the_activity();
					bp_get_template_part( 'activity/entry' );
				}
			}
			$activity = ob_get_clean();
		}
		header( 'Content-type: application/json' );
		echo json_encode(
			array(
				'code'     => $bpfb_code,
				'id'       => $activity_id,
				'activity' => $activity,
			)
		);
		exit();
	}


	/**
	 * Bypass default wp moderation to tweak link count in post content.
	 *
	 * @param bool   $bypass by pass.
	 * @param int    $user_id user id.
	 * @param string $title title.
	 * @param string $content content.
	 *
	 * @return bool
	 */
	public function bp_activity_link_moderation_custom( $bypass, $user_id, $title, $content ) {

		$_post     = array();
		$match_out = '';

		if ( ! empty( $user_id ) ) {

			// Get author data.
			$user = get_userdata( $user_id );

			// If data exists, map it.
			if ( ! empty( $user ) ) {
				$_post['author'] = $user->display_name;
				$_post['email']  = $user->user_email;
				$_post['url']    = $user->user_url;
			}
		}

		// Current user IP and user agent.
		$_post['user_ip'] = bp_core_current_user_ip();
		$_post['user_ua'] = bp_core_current_user_ua();

		// Post title and content.
		$_post['title']   = $title;
		$_post['content'] = $content;

		// Max links.
		$max_links = get_option( 'comment_max_links' );
		if ( ! empty( $max_links ) ) {

			$temp_content = str_replace( array( "image='http", "image=\"http" ), "", $content );

			// How many links?
			$num_links = preg_match_all( '/(http|ftp|https):\/\//i', $temp_content, $match_out );

			// Allow for bumping the max to include the user's URL.
			if ( ! empty( $_post['url'] ) ) {

				/**
				 * Filters the maximum amount of links allowed to include the user's URL.
				 *
				 * @param string $num_links How many links found.
				 * @param string $value User's url.
				 */
				$num_links = apply_filters( 'comment_max_links_url', $num_links, $_post['url'] );
			}

			if ( ( $num_links >= $max_links ) && $num_links != 1 ) {
				return false;
			}
		}

		// Get the moderation keys.
		$blacklist = trim( get_option( 'moderation_keys' ) );

		// Bail if blacklist is empty.
		if ( ! empty( $blacklist ) ) {

			// Get words separated by new lines.
			$words = explode( "\n", $blacklist );

			// Loop through words.
			foreach ( (array) $words as $word ) {

				// Trim the whitespace from the word.
				$word = trim( $word );

				// Skip empty lines.
				if ( empty( $word ) ) {
					continue;
				}

				// Do some escaping magic so that '#' chars in the
				// spam words don't break things:.
				$word    = preg_quote( $word, '#' );
				$pattern = "#$word#i";

				// Loop through post data.
				foreach ( $_post as $post_data ) {
					// Check each user data for current word.
					if ( preg_match( $pattern, $post_data ) ) {
						// Post does not pass.
						return false;
					}
				}
			}
		}

		// Check passed successfully.
		return true;
	}

	/**
	 * Generate embed code from the data.
	 *
	 * @param array $data data.
	 *
	 * @return string
	 */
	private function get_embed_shortcode( $data ) {
		if ( empty( $data ) ) {
			return '';
		}

		$bpfb_code = '';
		$codec     = new BPAPR_Shortcodes();

		if ( ! empty( $data['bpfb_video_url'] ) ) {
			$bpfb_code = $codec->create_video_tag( $data['bpfb_video_url'] );
		}

		if ( ! empty( $data['bpfb_link_url'] ) ) {
			$bpfb_code = $codec->create_link_tag(
				$data['bpfb_link_url'],
				$data['bpfb_link_title'],
				$data['bpfb_link_body'],
				$data['bpfb_link_image']
			);

			add_filter(
				'bp_bypass_check_for_moderation',
				array( $this, 'bp_activity_link_moderation_custom' ),
				10,
				4
			);
		}

		if ( ! empty( $data['bpfb_photos'] ) ) {
			$images    = $this->move_images( $data['bpfb_photos'] );
			$bpfb_code = $codec->create_images_tag( $images );
		}

		return $bpfb_code;
	}

	/**
	 * Image moving and resizing routine.
	 *
	 * Relies on WP built-in image resizing.
	 *
	 * @param array $images Image paths to move from temp directory.
	 *
	 * @return bool|array Array of new image paths, or (bool)false on failure.
	 */
	private function move_images( $images ) {

		if ( ! $images ) {
			return false;
		}

		if ( ! is_array( $images ) ) {
			$images = (array) $images;
		}

		global $bp;
		$ret = array();

		list( $thumb_w, $thumb_h ) = BPAPR_Data::get_thumbnail_size();

		$processed = 0;
		foreach ( $images as $img ) {
			$processed ++;
			if ( BPFB_IMAGE_LIMIT && $processed > BPFB_IMAGE_LIMIT ) {
				break;
			} // Do not even bother to process more.

			if ( preg_match( '!^https?:\/\/!i', $img ) ) { // Just add remote images.
				$ret[] = esc_url( $img );
				continue;
			}

			$prefix     = $bp->loggedin_user->id . '_' . preg_replace( '/[^0-9]/', '-', microtime() );
			$tmp_img = realpath( BPFB_TEMP_IMAGE_DIR . $img );
			$new_img = BPFB_BASE_IMAGE_DIR . "{$prefix}_{$img}";

			if ( @rename( $tmp_img, $new_img ) ) {

				$image = wp_get_image_editor( $new_img );
				if ( is_wp_error( $image ) ) {
					return false;
				}

				$thumb_filename = $image->generate_filename( 'bpfbt' );
				$image->resize( $thumb_w, $thumb_h, false );

				$type = function_exists( 'exif_imagetype' ) ? exif_imagetype( $new_img ) : '';

				// Alright, now let's rotate if we can.
				$exif = ( $type && IMAGETYPE_JPEG === $type ) && function_exists( 'exif_read_data' ) ? exif_read_data( $new_img ) : false;
				if ( $exif ) {
					if ( ! empty( $exif['Orientation'] ) && 3 === (int) $exif['Orientation'] ) {
						$image->rotate( 180 );
					} elseif ( ! empty( $exif['Orientation'] ) && 6 === (int) $exif['Orientation'] ) {
						$image->rotate( - 90 );
					} elseif ( ! empty( $exif['Orientation'] ) && 8 === (int) $exif['Orientation'] ) {
						$image->rotate( 90 );
					}
				}

				$image->save( $thumb_filename );

				$ret[] = pathinfo( $new_img, PATHINFO_BASENAME );
			} else {
				return false;
			} // Rename failure
		}

		return $ret;
	}
}
