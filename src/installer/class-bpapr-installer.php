<?php

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin installation.
 */
class BPAPR_Installer {

	/**
	 * Entry method.
	 *
	 * Handles Plugin installation.
	 *
	 * @access public
	 * @static
	 */
	public static function install() {
		$self = new self();
		if ( $self->prepare_paths() ) {
			$self->set_default_options();
		} else {
			$self->remove_default_options();
		}
	}

	/**
	 * Checks to see if we have the proper paths and if they're writable.
	 *
	 * @access private
	 */
	private static function check_paths() {
		if ( ! file_exists( BPFB_TEMP_IMAGE_DIR ) ) {
			return false;
		}
		if ( ! file_exists( BPFB_BASE_IMAGE_DIR ) ) {
			return false;
		}
		if ( ! is_writable( BPFB_TEMP_IMAGE_DIR ) ) {
			return false;
		}
		if ( ! is_writable( BPFB_BASE_IMAGE_DIR ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Prepares paths that will be used.
	 *
	 * @access private
	 */
	private function prepare_paths() {
		$ret = true;

		if ( ! file_exists( BPFB_TEMP_IMAGE_DIR ) ) {
			$ret = wp_mkdir_p( BPFB_TEMP_IMAGE_DIR );
		}
		if ( ! $ret ) {
			return false;
		}

		if ( ! file_exists( BPFB_BASE_IMAGE_DIR ) ) {
			$ret = wp_mkdir_p( BPFB_BASE_IMAGE_DIR );
		}

		return $ret;
	}

	/**
	 * (Re)sets Plugin options to defaults.
	 *
	 * @access private
	 */
	private function set_default_options() {
		$options = array(
			'installed' => 1,
		);
		update_option( 'bpfb_plugin', $options );
	}

	/**
	 * Removes plugin default options.
	 */
	private function remove_default_options() {
		delete_option( 'bpfb_plugin' );
	}
}
