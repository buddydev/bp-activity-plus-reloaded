<?php
/**
 * Data class
 *
 * @package    BuddyPress Activity Plus Reloaded
 * @subpackage Core
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Data.
 */
class Bpfb_Data {
	/**
	 * Singleton instance.
	 *
	 * @var Bpfb_Data
	 */
	private static $_instance;

	private function __construct() {
	}

	private function __clone() {
	}

	public static function get( $option, $fallback = false ) {
		if ( ! self::$_instance ) {
			self::_spawn_instance();
		}

		return self::$_instance->get( $option, $fallback );
	}

	public static function get_strict( $option, $fallback = false ) {
		if ( ! self::$_instance ) {
			self::_spawn_instance();
		}

		return self::$_instance->get_strict( $option, $fallback );
	}

	public static function get_thumbnail_size( $strict = false ) {
		if ( ! self::$_instance ) {
			self::_spawn_instance();
		}

		return self::$_instance->get_thumbnail_size( $strict );
	}

	private static function _spawn_instance() {
		if ( self::$_instance ) {
			return false;
		}
		self::$_instance = new Bpfb_Data_Container;
	}
}
