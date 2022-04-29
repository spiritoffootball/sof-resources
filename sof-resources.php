<?php
/**
 * Plugin Name: SOF Resources
 * Plugin URI: https://github.com/spiritoffootball/sof-resources
 * Description: Provides Resources and associated functionality.
 * Author: Christian Wach
 * Version: 0.1
 * Author URI: https://haystack.co.uk
 * Text Domain: sof-resources
 * Domain Path: /languages
 *
 * @package Spirit_Of_Football_Resources
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set our version here.
define( 'SOF_RESOURCES_VERSION', '0.1' );

// Store reference to this file.
if ( ! defined( 'SOF_RESOURCES_FILE' ) ) {
	define( 'SOF_RESOURCES_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'SOF_RESOURCES_URL' ) ) {
	define( 'SOF_RESOURCES_URL', plugin_dir_url( SOF_RESOURCES_FILE ) );
}

// Store PATH to this plugin's directory.
if ( ! defined( 'SOF_RESOURCES_PATH' ) ) {
	define( 'SOF_RESOURCES_PATH', plugin_dir_path( SOF_RESOURCES_FILE ) );
}

/**
 * SOF Resources Class.
 *
 * A class that encapsulates network-wide quotations.
 *
 * @since 0.1
 */
class Spirit_Of_Football_Resources {

	/**
	 * Custom Post Type object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpt The Custom Post Type object.
	 */
	public $cpt;

	/**
	 * Metaboxes object.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $metaboxes The Metaboxes object.
	 */
	public $metaboxes;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Include files.
		$this->include_files();

		// Setup globals.
		$this->setup_globals();

		// Register hooks.
		$this->register_hooks();

	}

	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// Include class files.
		include_once SOF_RESOURCES_PATH . 'includes/sof-resources-cpt.php';
		include_once SOF_RESOURCES_PATH . 'includes/sof-resources-metaboxes.php';

	}

	/**
	 * Set up objects.
	 *
	 * @since 0.1
	 */
	public function setup_globals() {

		// Instantiate objects.
		$this->cpt = new Spirit_Of_Football_Resources_CPT();
		$this->metaboxes = new Spirit_Of_Football_Resources_Metaboxes();

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Use translation.
		add_action( 'plugins_loaded', [ $this, 'translation' ] );

		// Hooks that always need to be present.
		$this->cpt->register_hooks();
		$this->metaboxes->register_hooks();

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Pass through.
		$this->cpt->activate();

	}

	/**
	 * Actions to perform on plugin deactivation NOT deletion.
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Pass through.
		$this->cpt->deactivate();

	}

	/**
	 * Loads translation.
	 *
	 * @since 0.1
	 */
	public function translation() {

		// Load translations.
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			'sof-resources', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( SOF_RESOURCES_FILE ) ) . '/languages/' // Relative path to files.
		);

	}

}

/**
 * Utility to get a reference to this plugin.
 *
 * @since 0.1
 *
 * @return Spirit_Of_Football_Resources $plugin The plugin reference.
 */
function spirit_of_football_resources() {

	// Store instance in static variable.
	static $plugin = false;

	// Maybe return instance.
	if ( false === $plugin ) {
		$plugin = new Spirit_Of_Football_Resources();
	}

	// --<
	return $plugin;

}

// Initialise plugin now.
spirit_of_football_resources();

// Activation.
register_activation_hook( __FILE__, [ spirit_of_football_resources(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ spirit_of_football_resources(), 'deactivate' ] );
