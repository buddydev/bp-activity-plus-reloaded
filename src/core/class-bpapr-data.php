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
class BPAPR_Data {

	/**
	 * Singleton instance.
	 *
	 * @var BPAPR_Data_Container
	 */
	private static $_instance = null;

	/**
	 * Prevent instantiation.
	 */
	private function __construct() {
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {
	}

	/**
	 * Get the container object.
	 *
	 * @param string $option options.
	 * @param bool   $fallback should use fallback.
	 *
	 * @return BPAPR_Data_Container
	 */
	public static function get( $option, $fallback = false ) {
		if ( ! self::$_instance ) {
			self::create_container();
		}

		return self::$_instance->get( $option, $fallback );
	}

	/**
	 * Get thumbnail dimension.
	 *
	 * @param bool $strict should disable value overrides.
	 *
	 * @return array
	 */
	public static function get_thumbnail_size( $strict = false ) {
		if ( ! self::$_instance ) {
			self::create_container();
		}

		return self::$_instance->get_thumbnail_size( $strict );
	}

	/**
	 * Create singleton container instance.
	 */
	private static function create_container() {

		if ( ! is_null( self::$_instance ) ) {
			return;
		}

		self::$_instance = new BPAPR_Data_Container();
	}
}
