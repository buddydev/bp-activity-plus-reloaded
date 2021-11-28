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
		add_action( 'bp_enqueue_scripts', array( $self, 'enqueue' ), 11 );
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
		wp_enqueue_script( 'bp-activity-plus-reloaded' );

		$data = (array) apply_filters( 'bpapr_localizable_data', $this->data );
		wp_localize_script( 'bp-activity-plus-reloaded', 'BPAPRJSData', $data );

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'bp-activity-plus-reloaded-uploader' );
		if ( ! current_theme_supports( 'bpfb_interface_style' ) ) {
			wp_enqueue_style( 'bp-activity-plus-reloaded' ); // backward compatibility.
		}

		if ( ! current_theme_supports( 'bpfb_toolbar_icons' ) ) { // back compat.
			wp_enqueue_style( 'bp-activity-plus-reloaded-toolbar' );
		}

		// back compat.
		do_action( 'bpfb_add_cssjs_hooks' );
	}

	/**
	 * Register vendor scripts.
	 */
	private function register_vendors() {

		$version = bpapr_activity_plus_reloaded()->version;

		wp_register_script( 'qq-file-uploader', BPFB_PLUGIN_URL . '/assets/js/external/fileuploader.js', array( 'jquery' ), $version );

	}

	/**
	 * Register core assets.
	 */
	private function register_core() {
		// @todo change later.
		$version = bpapr_activity_plus_reloaded()->version;

		wp_register_script( 'bp-activity-plus-reloaded', BPFB_PLUGIN_URL . '/assets/js/bp-activity-plus-reloaded.js', array( 'qq-file-uploader' ), $version );

		wp_register_style( 'bp-activity-plus-reloaded-uploader', BPFB_PLUGIN_URL . '/assets/css/external/fileuploader.css', false, $version );
		wp_register_style( 'bp-activity-plus-reloaded', BPFB_PLUGIN_URL . '/assets/css/bp-activity-plus-reloaded.css', false, $version );
		wp_register_style( 'bp-activity-plus-reloaded-toolbar', BPFB_PLUGIN_URL . '/assets/css/bp-activity-plus-reloaded-toolbar.css', false, $version );


		$this->data = array(
			'add_photos_tip'           => __( 'Add images', 'bp-activity-plus-reloaded' ),
			'add_photos'               => __( 'Submit images post', 'bp-activity-plus-reloaded' ),
			'add_remote_image'         => __( 'Add image URL', 'bp-activity-plus-reloaded' ),
			'add_another_remote_image' => __( 'Add another image URL', 'bp-activity-plus-reloaded' ),
			'add_videos'               => __( 'Add videos', 'bp-activity-plus-reloaded' ),
			'add_video'                => __( 'Submit video post', 'bp-activity-plus-reloaded' ),
			'add_links'                => __( 'Add links', 'bp-activity-plus-reloaded' ),
			'add_link'                 => __( 'Submit link post', 'bp-activity-plus-reloaded' ),
			'add'                      => __( 'Add', 'bp-activity-plus-reloaded' ),
			'cancel'                   => __( 'Cancel', 'bp-activity-plus-reloaded' ),
			'preview'                  => __( 'Preview', 'bp-activity-plus-reloaded' ),
			'drop_files'               => __( 'Drop files here to upload', 'bp-activity-plus-reloaded' ),
			'upload_file'              => __( 'Upload a file', 'bp-activity-plus-reloaded' ),
			'choose_thumbnail'         => __( 'Choose thumbnail', 'bp-activity-plus-reloaded' ),
			'no_thumbnail'             => __( 'No thumbnail', 'bp-activity-plus-reloaded' ),
			'paste_video_url'          => __( 'Paste video URL here', 'bp-activity-plus-reloaded' ),
			'paste_link_url'           => __( 'Paste link here', 'bp-activity-plus-reloaded' ),
			'images_limit_exceeded'    => sprintf( __( "You tried to add too many images, only %d will be posted.", 'bp-activity-plus-reloaded' ), BPFB_IMAGE_LIMIT ),
			// Variables.
			'_max_images'              => BPFB_IMAGE_LIMIT,
			'isGroup'                  => bp_is_group() ? 1 : 0,
			'groupID'                  => bp_is_group() ? bp_get_current_group_id() : 0,
			'show_upload_buttons'      => 1,
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
				'root_url'      => BPFB_PLUGIN_URL,
				'temp_img_url'  => BPFB_TEMP_IMAGE_URL,
				'base_img_url'  => BPFB_BASE_IMAGE_URL,
				'theme'         => BPAPR_Data::get( 'theme', 'default' ),
				'alignment'     => BPAPR_Data::get( 'alignment', 'left' ),
				'allowed_items' => BPAPR_Data::get( 'allowed_items', array( 'photos', 'videos', 'links' ) ),
			)
		);

		printf( '<script type="text/javascript">var BPAPRConfig=%s;</script>', json_encode( $data ) );

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
