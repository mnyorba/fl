<?php
/*
Plugin Name: Flexi Real Estate
Description: Custom post type for real estate with taxonomies and ACF fields
Version: 1.0
Author: mnyorba
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin name
define( 'FLEXIREALE_NAME', 'Flexi Real Estate' );

// Plugin version
define( 'FLEXIREALE_VERSION', '1.0.0' );

// Plugin Root File
define( 'FLEXIREALE_PLUGIN_FILE', __FILE__ );

// Plugin base
define( 'FLEXIREALE_PLUGIN_BASE', plugin_basename( FLEXIREALE_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'FLEXIREALE_PLUGIN_DIR', plugin_dir_path( FLEXIREALE_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'FLEXIREALE_PLUGIN_URL', plugin_dir_url( FLEXIREALE_PLUGIN_FILE ) );

// Include necessary files
require_once FLEXIREALE_PLUGIN_DIR . 'includes/trait-singleton.php';
require_once FLEXIREALE_PLUGIN_DIR . 'includes/class-abstract-post-type.php';

require_once FLEXIREALE_PLUGIN_DIR . 'src/classes/class-real-estate-post-type.php';
require_once FLEXIREALE_PLUGIN_DIR . 'src/classes/class-district-taxonomy.php';
require_once FLEXIREALE_PLUGIN_DIR . 'src/classes/class-real-estate-acf-fields.php';
require_once FLEXIREALE_PLUGIN_DIR . 'src/classes/class-real-estate-ajax-filter.php';

require_once FLEXIREALE_PLUGIN_DIR . 'includes/class-plugin-deactivator.php';

// Initialize the plugin
add_action( 'plugins_loaded', array( 'Flexi_Real_Estate', 'get_instance' ) );

class Flexi_Real_Estate {
	use Singleton;

	protected function __construct() {
		$this->init_hooks();
	}

	protected function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init_post_type' ) );
		add_action( 'init', array( $this, 'init_taxonomy' ) );
		add_action( 'init', array( $this, 'init_acf_fields' ) );
		add_action( 'init', array( $this, 'maybe_flush_rewrite_rules' ), 99 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_ajax_flexi_real_estate_keep_data', array( $this, 'ajax_keep_data' ) );

		self::include_functions();

		register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( __FILE__, array( 'Plugin_Deactivator', 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( 'Plugin_Deactivator', 'uninstall' ) );

		self::init_ajax_filter();
	}

	public function init_post_type() {
		Real_Estate_Post_Type::get_instance();
	}

	public function init_taxonomy() {
		District_Taxonomy::get_instance();
	}

	public function init_acf_fields() {
		Real_Estate_ACF_Fields::get_instance();
	}

	public function init_ajax_filter() {
		Real_Estate_Ajax_Filter_Widget::get_instance();
	}

	private function include_functions() {
		$files = glob( FLEXIREALE_PLUGIN_DIR . 'src/functions/*.php' );
		$files = apply_filters( 'include_functions', $files );
		if ( ! empty( $files ) ) {
			foreach ( $files as $file ) {
				require_once $file;
			}
		}
	}

	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'flexi-real-estate-admin', FLEXIREALE_PLUGIN_URL . 'assets/js/admin-script.js', array( 'jquery' ), '1.0', true );
		wp_localize_script(
			'flexi-real-estate-admin',
			'flexiRealEstateAdmin',
			array(
				'nonce' => wp_create_nonce( 'flexi_real_estate_keep_data' ),
			)
		);
	}

	public function ajax_keep_data() {
		check_ajax_referer( 'flexi_real_estate_keep_data' );
		update_option( 'flexi_real_estate_keep_data', true );
		wp_die();
	}
	public function load_textdomain() {
		load_plugin_textdomain( 'flexi-real-estate', false, plugin_basename( __DIR__ . '/languages/' ) );
	}

	public static function activate() {
		// Ensure post types and taxonomies are registered
		self::get_instance()->init_post_type();
		self::get_instance()->init_taxonomy();

		// Flush rewrite rules
		flush_rewrite_rules();
	}

	public function maybe_flush_rewrite_rules() {
		if ( get_option( 'flexi_real_estate_flush_rewrite_rules' ) == 'yes' ) {
			flush_rewrite_rules();
			delete_option( 'flexi_real_estate_flush_rewrite_rules' );
		}
	}
}
