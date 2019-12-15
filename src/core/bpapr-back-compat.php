<?php
/**
 * Backward compativility.
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
 * Use functions with 'bpapr_'. prefix.
 *
 * @param int $blog_id blog id.
 *
 * @return string
 * @deprecated
 *
 */
function bpfb_get_image_url( $blog_id ) {
	return bpapr_get_image_url( $blog_id );
}

/**
 * Get absolute path to image directory for the current blog.
 *
 * @param int $blog_id blog id.
 *
 * @return string
 */
function bpfb_get_image_dir( $blog_id ) {
	return bpapr_get_image_dir( $blog_id );
}