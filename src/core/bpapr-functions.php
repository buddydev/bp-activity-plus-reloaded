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

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Get image url.
 *
 * @param int $blog_id blog id.
 *
 * @return string
 */
function bpapr_get_image_url( $blog_id ) {
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
function bpapr_get_image_dir( $blog_id ) {
	if ( ! $blog_id || ! defined( 'BP_ENABLE_MULTIBLOG' ) || ! BP_ENABLE_MULTIBLOG ) {
		return BPFB_BASE_IMAGE_DIR;
	}

	switch_to_blog( $blog_id );
	$wp_upload_dir = wp_upload_dir();
	restore_current_blog();

	return $wp_upload_dir['basedir'] . '/bpfb/';
}

/**
 * Sanitizes the path and expands it into full form.
 *
 * @param string $file Relative file path.
 *
 * @return mixed Sanitized path, or (bool)false on failure
 */
function bpapr_get_resolved_temp_path( $file ) {
	$file = ltrim( $file, '/' );

	// No subdirs in path, so we can do this quick check too.
	if ( basename( $file ) !== $file ) {
		return false;
	}

	$tmp_path = trailingslashit( wp_normalize_path( realpath( BPFB_TEMP_IMAGE_DIR ) ) );
	if ( empty( $tmp_path ) ) {
		return false;
	}

	$full_path = wp_normalize_path( realpath( $tmp_path . $file ) );
	if ( empty( $full_path ) ) {
		return false;
	}

	// Are we still within our defined TMP dir?
	$rx        = preg_quote( $tmp_path, '/' );
	$full_path = preg_match( "/^{$rx}/", $full_path )
		? $full_path
		: false;
	if ( empty( $full_path ) ) {
		return false;
	}

	// Also, does this resolve to an actual file?
	return file_exists( $full_path )
		? $full_path
		: false;
}

/**
 * Get supported image extensions.
 *
 * @return array Supported image extensions
 */
function bpapr_get_supported_image_extensions() {
	return array( 'jpg', 'jpeg', 'png', 'gif' );
}