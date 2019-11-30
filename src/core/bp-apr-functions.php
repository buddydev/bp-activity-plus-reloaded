<?php
/**
 * Core functions.
 *
 * @package    BuddyPress Activity Plus Reloaded
 * @subpackage Core
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

/**
 * Get image url.
 *
 * @param int $blog_id blog id.
 *
 * @return string
 */
function bpfb_get_image_url( $blog_id ) {
	if ( ! $blog_id || ! defined( 'BP_ENABLE_MULTIBLOG' ) || ! BP_ENABLE_MULTIBLOG ) {
		return str_replace( 'http://', BPFB_PROTOCOL, BPFB_BASE_IMAGE_URL );
	}

	switch_to_blog( $blog_id );
	$wp_upload_dir = wp_upload_dir();
	restore_current_blog();

	return str_replace( 'http://', BPFB_PROTOCOL, $wp_upload_dir['baseurl'] ) . '/bpfb/';
}

/**
 * Get absolute path to image directory for the current blog.
 *
 * @param int $blog_id blog id.
 *
 * @return string
 */
function bpfb_get_image_dir( $blog_id ) {
	if ( ! $blog_id || ! defined( 'BP_ENABLE_MULTIBLOG' ) || ! BP_ENABLE_MULTIBLOG ) {
		return BPFB_BASE_IMAGE_DIR;
	}

	switch_to_blog( $blog_id );
	$wp_upload_dir = wp_upload_dir();
	restore_current_blog();

	return $wp_upload_dir['basedir'] . '/bpfb/';
}
