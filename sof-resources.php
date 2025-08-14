<?php
/**
 * SOF Resources
 *
 * Plugin Name: SOF Resources
 * Description: Provides Resources and associated functionality.
 * Plugin URI:  https://github.com/spiritoffootball/sof-resources
 * Version:     1.0.1a
 * Author:      Christian Wach
 * Author URI:  https://haystack.co.uk
 * Text Domain: sof-resources
 * Domain Path: /languages
 *
 * @package Spirit_Of_Football_Resources
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set our version here.
define( 'SOF_RESOURCES_VERSION', '1.0.1a' );

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
	 * @var Spirit_Of_Football_Resources_CPT
	 */
	public $cpt;

	/**
	 * Metaboxes object.
	 *
	 * @since 0.1
	 * @access public
	 * @var Spirit_Of_Football_Resources_Metaboxes
	 */
	public $metaboxes;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Initialise once plugins are loaded.
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialise.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap plugin.
		$this->include_files();
		$this->setup_globals();
		$this->register_hooks();

		/**
		 * Broadcast that this plugin is now loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_resources/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// Include class files.
		require SOF_RESOURCES_PATH . 'includes/class-cpt.php';
		require SOF_RESOURCES_PATH . 'includes/class-metaboxes.php';

	}

	/**
	 * Set up objects.
	 *
	 * @since 0.1
	 */
	public function setup_globals() {

		// Instantiate objects.
		$this->cpt       = new Spirit_Of_Football_Resources_CPT();
		$this->metaboxes = new Spirit_Of_Football_Resources_Metaboxes();

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Use translation.
		add_action( 'init', [ $this, 'translation' ] );

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Make sure plugin is bootstrapped.
		$this->initialise();

		/**
		 * Fires when this plugin has been activated.
		 *
		 * Used internally by:
		 *
		 * * Spirit_Of_Football_Resources_CPT::activate() (Priority: 10)
		 *
		 * @since 0.4.1
		 */
		do_action( 'sof_resources/activated' );

	}

	/**
	 * Actions to perform on plugin deactivation NOT deletion.
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Make sure plugin is bootstrapped.
		$this->initialise();

		/**
		 * Fires when this plugin has been deactivated.
		 *
		 * Used internally by:
		 *
		 * * Spirit_Of_Football_Resources_CPT::deactivate() (Priority: 10)
		 *
		 * @since 0.4.1
		 */
		do_action( 'sof_resources/deactivated' );

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
