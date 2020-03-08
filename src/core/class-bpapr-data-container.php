<?php
/**
 * Data container
 *
 * @package    BuddyPress Activity Plus Reloaded
 * @subpackage Core
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Data container class.
 */
class BPAPR_Data_Container {

	/**
	 * Settings.
	 *
	 * @var array
	 */
	private $_data;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$data        = get_option( 'bpfb', array() );
		$this->_data = wp_parse_args(
			$data,
			array(
				'oembed_width'   => 450,
				'image_limit'    => 5,
				'links_target'   => false,
				'cleanup_images' => false,
				'link_target'    => '',
			)
		);
	}

	/**
	 * Get option.
	 *
	 * @param string $option option name.
	 * @param bool   $fallback fallback value.
	 *
	 * @return bool|mixed
	 */
	public function get( $option, $fallback = false ) {
		$define = 'BPFB_' . strtoupper( $option );
		if ( defined( $define ) ) {
			return constant( $define );
		}

		return $this->_get( $option, $fallback );
	}

	/**
	 * Get thumbnail size.
	 *
	 * @param bool $strict disable override by constants.
	 *
	 * @return array
	 */
	public function get_thumbnail_size( $strict = false ) {
		$thumb_w = empty( $this->_data['thumbnail_size_width'] ) || ! (int) $this->_data['thumbnail_size_width']
			? get_option( 'thumbnail_size_w', 100 )
			: (int) $this->_data['thumbnail_size_width'];
		$thumb_w = $thumb_w ? $thumb_w : 100;
		$thumb_h = empty( $this->_data['thumbnail_size_height'] ) || ! (int) $this->_data['thumbnail_size_height']
			? get_option( 'thumbnail_size_h', 100 )
			: (int) $this->_data['thumbnail_size_height'];
		$thumb_h = $thumb_h ? $thumb_h : 100;

		// Override thumbnail image size in wp-config.php.
		if ( ! $strict && defined( 'BPFB_THUMBNAIL_IMAGE_SIZE' ) ) {
			list( $tw, $th ) = explode( 'x', BPFB_THUMBNAIL_IMAGE_SIZE );

			$thumb_w = (int) $tw ? (int) $tw : $thumb_w;
			$thumb_h = (int) $th ? (int) $th : $thumb_h;
		}

		return array( $thumb_w, $thumb_h );
	}

	/*
	 * Get option.
	 */
	private function _get( $option, $fallback = false ) {
		if ( isset( $this->_data[ $option ] ) ) {
			return $this->_data[ $option ];
		}

		return $fallback;
	}
}
