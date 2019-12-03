<?php
/**
 * Plugin Name: BuddyPress Activity Plus Reloaded
 * Plugin URI: http://premium.wpmudev.org/project/media-embeds-for-buddypress-activity
 * Description: A Facebook-style media sharing improvement for the activity box.
 * Version: 1.0.0
 * Author: BuddyDev
 * Author URI: https://buddydev.com
*/

/*
 * Credit:
 * This plugin is a fork of BuddyPress Activity Plus plugin by WPMUDEV(https://premium.wpmudev.org/).
 * Copyright 2009-2011 Incsub (http://incsub.com)
 * Author - Ve Bailovity (Incsub)
 * Designed by Brett Sirianni (The Edge)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
 * the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define( 'BPFB_PLUGIN_SELF_DIRNAME', basename( dirname( __FILE__ ) ) );
define( 'BPFB_PROTOCOL', ( is_ssl() ? 'https://' : 'http://' ) );

define( 'BPFB_PLUGIN_BASE_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'BPFB_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

load_plugin_textdomain( 'bpfb', false, BPFB_PLUGIN_SELF_DIRNAME . '/languages/' );

// Override image limit in wp-config.php.
if ( ! defined( 'BPFB_IMAGE_LIMIT' ) ) {
	define( 'BPFB_IMAGE_LIMIT', 5 );
}

// Override link target preference in wp-config.php.
if ( ! defined( 'BPFB_LINKS_TARGET' ) ) {
	define( 'BPFB_LINKS_TARGET', false );
}

$wp_upload_dir = wp_upload_dir();
define( 'BPFB_TEMP_IMAGE_DIR', $wp_upload_dir['basedir'] . '/bpfb/tmp/' );
define( 'BPFB_TEMP_IMAGE_URL', $wp_upload_dir['baseurl'] . '/bpfb/tmp/' );
define( 'BPFB_BASE_IMAGE_DIR', $wp_upload_dir['basedir'] . '/bpfb/' );
define( 'BPFB_BASE_IMAGE_URL', $wp_upload_dir['baseurl'] . '/bpfb/' );

// Hook up the installation routine and check if we're really, really set to go.
require_once BPFB_PLUGIN_BASE_DIR . '/src/installer/class-bpfb-installer.php';
register_activation_hook( __FILE__, array( 'BPFB_Installer', 'install' ) );

/**
 * Includes the core requirements and serves the improved activity box.
 */
function bpfb_plugin_init() {

	require_once BPFB_PLUGIN_BASE_DIR . '/src/core/class-bpfb-data-container.php';
	require_once BPFB_PLUGIN_BASE_DIR . '/src/core/class-bpfb-data.php';
	require_once BPFB_PLUGIN_BASE_DIR . '/src/core/bp-apr-functions.php';

	require_once BPFB_PLUGIN_BASE_DIR . '/src/bootstrap/class-bp-apr-assets-loader.php';

	require_once BPFB_PLUGIN_BASE_DIR . '/src/handlers/class-bpapr-activity-update-handler.php';
	require_once BPFB_PLUGIN_BASE_DIR . '/src/handlers/class-bpapr-preview-handler.php';
	require_once BPFB_PLUGIN_BASE_DIR . '/src/handlers/class-bpapr-delete-handler.php';

	require_once( BPFB_PLUGIN_BASE_DIR . '/src/shortcodes/class-bpfb-shortcodes.php' );

	// Group Documents integration.
	if ( defined( 'BP_GROUP_DOCUMENTS_IS_INSTALLED' ) && BP_GROUP_DOCUMENTS_IS_INSTALLED ) {
		require_once( BPFB_PLUGIN_BASE_DIR . '/lib/bpfb_group_documents.php' );
	}

	if ( is_admin() ) {
		require_once BPFB_PLUGIN_BASE_DIR . '/src/admin/class-bpfb-admin.php';
		BPFB_Admin::boot();
	}

	do_action( 'bpfb_init' );
}

// Only fire off if BP is actually loaded.
add_action( 'bp_loaded', 'bpfb_plugin_init' );

/**
 * Setup.
 */
function bpfb_plugin_setup() {
	BPAPR_Preview_Handler::boot();
	BPAPR_Activity_Update_Handler::boot();
	BPAPR_Delete_Handler::boot();

	BPAPR_Assets_Loader::boot();
	BPFB_Shortcodes::register();
}

add_action( 'bp_loaded', 'bpfb_plugin_setup' );
