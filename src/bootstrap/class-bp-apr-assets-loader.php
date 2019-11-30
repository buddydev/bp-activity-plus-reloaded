<?php
/**
 * Assets Loader
 *
 * @package    BuddyPress Activity Plus Reloaded
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Assets Loader.
 */
class BPAPR_Assets_Loader {

	/**
	 * Data to be send as localized js.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Boot it.
	 */
	public static function boot() {
		$self = new self();
		add_action( 'bp_enqueue_scripts', array( $self, 'register' ) );
		add_action( 'bp_enqueue_scripts', array( $self, 'enqueue' ) );
		add_action( 'wp_head', array( $self, 'load_extra_configs' ) );
	}

	/**
	 * Register assets.
	 */
	public function register() {
		$this->register_vendors();
		$this->register_core();
	}

	/**
	 * Load assets.
	 */
	public function enqueue() {
		if ( ! is_user_logged_in() || ! $this->needs_loading() ) {
			return;
		}

		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'bpfb_interface_script' );

		wp_localize_script( 'bpfb_interface_script', 'l10nBpfb', $this->data );

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'file_uploader_style' );
		if ( ! current_theme_supports( 'bpfb_interface_style' ) ) {
			wp_enqueue_style( 'bpfb_interface_style' );
		}

		if ( ! current_theme_supports( 'bpfb_toolbar_icons' ) ) {
			wp_enqueue_style( 'bpfb_toolbar_icons' );
		}

		// back compat.
		do_action('bpfb_add_cssjs_hooks');
	}

	/**
	 * Register vendor scripts.
	 */
	private function register_vendors() {

		$version = '1.0.0';

		wp_register_script( 'file_uploader', BPFB_PLUGIN_URL . '/assets/js/external/fileuploader.js', array( 'jquery' ), $version );

	}

	/**
	 * Register core assets.
	 */
	private function register_core() {
		// @todo change later.
		$version = '1.0.0';

		wp_register_script( 'bpfb_interface_script', BPFB_PLUGIN_URL . '/assets/js/bpfb_interface.js', array( 'file_uploader' ), $version );

		wp_register_style( 'file_uploader_style', BPFB_PLUGIN_URL . '/assets/css/external/fileuploader.css', false, $version );
		wp_register_style( 'bpfb_interface_style', BPFB_PLUGIN_URL . '/assets/css/bpfb_interface.css', false, $version );
		wp_register_style( 'bpfb_toolbar_icons', BPFB_PLUGIN_URL . '/assets/css/bpfb_toolbar.css', false, $version );


		$this->data = array(
			'add_photos_tip'           => __( 'Add images', 'bpfb' ),
			'add_photos'               => __( 'Submit images post', 'bpfb' ),
			'add_remote_image'         => __( 'Add image URL', 'bpfb' ),
			'add_another_remote_image' => __( 'Add another image URL', 'bpfb' ),
			'add_videos'               => __( 'Add videos', 'bpfb' ),
			'add_video'                => __( 'Submit video post', 'bpfb' ),
			'add_links'                => __( 'Add links', 'bpfb' ),
			'add_link'                 => __( 'Submit link post', 'bpfb' ),
			'add'                      => __( 'Add', 'bpfb' ),
			'cancel'                   => __( 'Cancel', 'bpfb' ),
			'preview'                  => __( 'Preview', 'bpfb' ),
			'drop_files'               => __( 'Drop files here to upload', 'bpfb' ),
			'upload_file'              => __( 'Upload a file', 'bpfb' ),
			'choose_thumbnail'         => __( 'Choose thumbnail', 'bpfb' ),
			'no_thumbnail'             => __( 'No thumbnail', 'bpfb' ),
			'paste_video_url'          => __( 'Paste video URL here', 'bpfb' ),
			'paste_link_url'           => __( 'Paste link here', 'bpfb' ),
			'images_limit_exceeded'    => sprintf( __( "You tried to add too many images, only %d will be posted.", 'bpfb' ), BPFB_IMAGE_LIMIT ),
			// Variables
			'_max_images'              => BPFB_IMAGE_LIMIT,
		);

	}

	/**
	 * Introduces `plugins_url()` and other significant URLs as root variables (global).
	 */
	public function load_extra_configs() {

		if ( ! function_exists( 'buddypress' ) ) {
			return;
		}

		$data = apply_filters(
			'bpfb_js_data_object',
			array(
				'root_url'     => BPFB_PLUGIN_URL,
				'temp_img_url' => BPFB_TEMP_IMAGE_URL,
				'base_img_url' => BPFB_BASE_IMAGE_URL,
				'theme'        => BPFB_Data::get( 'theme', 'default' ),
				'alignment'    => BPFB_Data::get( 'alignment', 'left' ),
			)
		);
		printf( '<script type="text/javascript">var _bpfb_data=%s;</script>', json_encode( $data ) );

		if ( 'default' === $data['theme'] || current_theme_supports( 'bpfb_toolbar_icons' ) ) {
			return;
		}


		$url = BPFB_PLUGIN_URL;
		?>
		<style type="text/css">
			@font-face {
				font-family: 'bpfb';
				src: url('<?php echo $url;?>/assets/css/external/font/bpfb.eot');
				src: url('<?php echo $url;?>/assets/css/external/font/bpfb.eot?#iefix') format('embedded-opentype'),
				url('<?php echo $url;?>/assets/css/external/font/bpfb.woff') format('woff'),
				url('<?php echo $url;?>/assets/css/external/font/bpfb.ttf') format('truetype'),
				url('<?php echo $url;?>/assets/css/external/font/bpfb.svg#icomoon') format('svg');
				font-weight: normal;
				font-style: normal;
			}
		</style>
		<?php
	}


	/**
	 * Load admin css.
	 */
	public function admin_enqueue_styles() {
	}

	/**
	 * Do we need to load.
	 *
	 * @return bool
	 */
	private function needs_loading() {
		$enabled = bp_is_activity_component() || bp_is_user_activity() || bp_is_group_activity();

		return apply_filters( 'bpfb_inject_dependencies', $enabled );
	}
}
