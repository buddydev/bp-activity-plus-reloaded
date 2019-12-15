<?php
/**
 * Short Description
 *
 * @package    BP5_Dev
 * @subpackage ${NAMESPACE}
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

/**
 * Class Ajax_Request_Handler
 */
class BPAPR_Delete_Handler {

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

		if ( BPAPR_Data::get( 'cleanup_images' ) ) {
			add_action( 'bp_before_activity_delete', array( $this, 'remove_activity_images' ) );
		}
	}

	/**
	 * Trigger handler when BuddyPress activity is removed.
	 *
	 * @param array $args BuddyPress activity arguments.
	 *
	 * @return bool Insignificant
	 */
	public function remove_activity_images( $args ) {

		if ( empty( $args['id'] ) || ! is_user_logged_in() ) {
			return false;
		}

		// Compatibility with BP Reshare.
		if ( $args['type'] == 'reshare_update' ) {
			return false;
		}

		$activity = new BP_Activity_Activity( $args['id'] );

		if ( ! is_object( $activity ) || empty( $activity->content ) ) {
			return false;
		}

		if ( ! bp_activity_user_can_delete( $activity ) ) {
			return false;
		}

		if ( ! BPAPR_Shortcodes::has_images( $activity->content ) ) {
			return false;
		}

		$matches = array();
		preg_match( '/\[bpfb_images\](.*?)\[\/bpfb_images\]/s', $activity->content, $matches );
		if ( empty( $matches[1] ) ) {
			return false;
		}

		$this->_clean_up_content_images( $matches[1], $activity );

		return true;
	}

	/**
	 * Callback for activity images removal
	 *
	 * @param string               $content content.
	 * @param BP_Activity_Activity $activity activity.
	 *
	 * @return bool
	 */
	private function _clean_up_content_images( $content, $activity ) {

		if ( ! BPAPR_Data::get( 'cleanup_images' ) ) {
			return false;
		}

		if ( ! bp_activity_user_can_delete( $activity ) ) {
			return false;
		}

		$images = BPAPR_Shortcodes::extract_images( $content );

		if ( empty( $images ) ) {
			return false;
		}

		$activity_blog_id = get_current_blog_id();

		foreach ( $images as $image ) {
			$info = pathinfo( trim( $image ) );

			// Make sure we have the info we need.
			if ( empty( $info['filename'] ) || empty( $info['extension'] ) ) {
				continue;
			}

			// Make sure we're dealing with the image.
			$ext = strtolower( $info['extension'] );

			if ( ! in_array( $ext, bpapr_get_supported_image_extensions() ) ) {
				continue;
			}

			// Construct the filenames.
			$thumbnail = bpapr_get_image_dir( $activity_blog_id ) . $info['filename'] . '-bpfbt.' . $ext;
			$full      = bpapr_get_image_dir( $activity_blog_id ) . trim( $image );

			// Actually remove the images.
			if ( file_exists( $thumbnail ) && is_writable( $thumbnail ) ) {
				@unlink( $thumbnail );
			}
			if ( file_exists( $full ) && is_writable( $full ) ) {
				@unlink( $full );
			}
		}

		return true;
	}
}
