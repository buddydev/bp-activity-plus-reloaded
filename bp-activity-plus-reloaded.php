<?php
/**
 * Plugin Name: Activity Plus Reloaded for BuddyPress
 * Plugin URI: https://buddydev.com/plugins/bp-activity-plus-reloaded/
 * Description: A Facebook-style media sharing improvement for the activity box.
 * Version: 1.1.1
 * Author: BuddyDev
 * Author URI: https://buddydev.com
 *
 * Text Domain: bp-activity-plus-reloaded
 * Domain Path: /languages
 * License:     GPLv2 or later (license.txt)
 *
 * @package BuddyPress_Activity_Plus_reloaded
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

// For backward compatibility, we are not renaming the constants.
define( 'BPFB_PLUGIN_SELF_DIRNAME', basename( dirname( __FILE__ ) ) );
define( 'BPFB_PROTOCOL', ( is_ssl() ? 'https://' : 'http://' ) );

define( 'BPFB_PLUGIN_BASE_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'BPFB_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

load_plugin_textdomain( 'bp-activity-plus-reloaded', false, BPFB_PLUGIN_SELF_DIRNAME . '/languages/' );

$wp_upload_dir = wp_upload_dir();
define( 'BPFB_TEMP_IMAGE_DIR', $wp_upload_dir['basedir'] . '/bpfb/tmp/' );
define( 'BPFB_TEMP_IMAGE_URL', $wp_upload_dir['baseurl'] . '/bpfb/tmp/' );
define( 'BPFB_BASE_IMAGE_DIR', $wp_upload_dir['basedir'] . '/bpfb/' );
define( 'BPFB_BASE_IMAGE_URL', $wp_upload_dir['baseurl'] . '/bpfb/' );

/**
 * Helper.
 *
 * @property-read string                $path absolute path to the plugin directory.
 * @property-read string                $url absolute url to the plugin directory.
 * @property-read string                $basename plugin base name.
 * @property-read string                $version plugin version.
 */
class BPAPR_Activity_Plus_Reloaded {

	/**
	 * Plugin Version.
	 *
	 * @var string
	 */
	private $version = '1.1.1';

	/**
	 * Class instance
	 *
	 * @var static
	 */
	private static $instance = null;

	/**
	 * Plugins directory path
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Plugins directory url
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Plugin Basename.
	 *
	 * @var string
	 */
	private $basename;

	/**
	 * Protected properties. These properties are inaccessible via magic method.
	 *
	 * @var array
	 */
	private static $guarded = array( 'instance' );

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->bootstrap();
	}

	/**
	 * Get class instance
	 *
	 * @return BPAPR_Activity_Plus_Reloaded
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap the core.
	 */
	private function bootstrap() {
		// Setup general properties.
		$this->path     = plugin_dir_path( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->basename = plugin_basename( __FILE__ );

		// Only fire off if BP is actually loaded.
		add_action( 'bp_loaded', array( $this, 'setup_constants' ) );

		add_action( 'bp_loaded', array( $this, 'load' ) );

		add_action( 'bp_loaded', array( $this, 'setup' ) );

		require_once BPFB_PLUGIN_BASE_DIR . '/src/installer/class-bpapr-installer.php';
		register_activation_hook( __FILE__, array( 'BPAPR_Installer', 'install' ) );
	}

	/**
	 * Sets up functionality constants.
	 */
	public function setup_constants() {
		// Override image limit in wp-config.php.
		if ( ! defined( 'BPFB_IMAGE_LIMIT' ) ) {
			define( 'BPFB_IMAGE_LIMIT', 5 );
		}
	}

	/**
	 * Load dependencies.
	 */
	public function load() {

		require_once BPFB_PLUGIN_BASE_DIR . '/src/core/class-bpapr-data-container.php';
		require_once BPFB_PLUGIN_BASE_DIR . '/src/core/class-bpapr-data.php';
		require_once BPFB_PLUGIN_BASE_DIR . '/src/core/bpapr-functions.php';
		require_once BPFB_PLUGIN_BASE_DIR . '/src/core/bpapr-back-compat.php';

		require_once BPFB_PLUGIN_BASE_DIR . '/src/bootstrap/class-bpapr-assets-loader.php';

		require_once BPFB_PLUGIN_BASE_DIR . '/src/handlers/class-bpapr-activity-update-handler.php';
		require_once BPFB_PLUGIN_BASE_DIR . '/src/handlers/class-bpapr-preview-handler.php';
		require_once BPFB_PLUGIN_BASE_DIR . '/src/handlers/class-bpapr-delete-handler.php';

		require_once BPFB_PLUGIN_BASE_DIR . '/src/shortcodes/class-bpapr-shortcodes.php';

		// Group Documents integration.
		if ( defined( 'BP_GROUP_DOCUMENTS_IS_INSTALLED' ) && BP_GROUP_DOCUMENTS_IS_INSTALLED ) {
			// require_once BPFB_PLUGIN_BASE_DIR . '/lib/class-bpapr-group-documents.php';
		}

		if ( is_admin() ) {
			require_once BPFB_PLUGIN_BASE_DIR . '/src/admin/class-bpapr-admin.php';
			BPAPR_Admin::boot();
		}

		do_action( 'bpapr_loaded' );
	}

	/**
	 * Load plugin core files and assets.
	 */
	public function setup() {
		BPAPR_Preview_Handler::boot();
		BPAPR_Activity_Update_Handler::boot();
		BPAPR_Delete_Handler::boot();

		BPAPR_Assets_Loader::boot();
		BPAPR_Shortcodes::register();
	}

	/**
	 * On activation create table
	 */
	public function on_activation() {

	}

	/**
	 * Deactivation.
	 */
	public function on_deactivation() {
	}

	/**
	 * Magic method for accessing property as readonly(It's a lie, references can be updated).
	 *
	 * @param string $name property name.
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {

		if ( ! in_array( $name, self::$guarded, true ) && property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		return null;
	}
}

/**
 * Helper.
 *
 * @return BPAPR_Activity_Plus_reloaded
 */
function bpapr_activity_plus_reloaded() {
	return BPAPR_Activity_Plus_Reloaded::get_instance();
}

bpapr_activity_plus_reloaded();
