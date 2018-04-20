<?php /*
--------------------------------------------------------------------------------
Plugin Name: SOF Resources
Plugin URI: http://spiritoffootball.com
Description: Provides Resources and associated functionality.
Author: Christian Wach
Version: 0.1
Author URI: http://haystack.co.uk
--------------------------------------------------------------------------------
*/


// set our version here
define( 'SOF_RESOURCES_VERSION', '0.1' );

// store reference to this file
if ( ! defined( 'SOF_RESOURCES_FILE' ) ) {
	define( 'SOF_RESOURCES_FILE', __FILE__ );
}

// store URL to this plugin's directory
if ( ! defined( 'SOF_RESOURCES_URL' ) ) {
	define( 'SOF_RESOURCES_URL', plugin_dir_url( SOF_RESOURCES_FILE ) );
}

// store PATH to this plugin's directory
if ( ! defined( 'SOF_RESOURCES_PATH' ) ) {
	define( 'SOF_RESOURCES_PATH', plugin_dir_path( SOF_RESOURCES_FILE ) );
}



/**
 * SOF Resources Class
 *
 * A class that encapsulates network-wide quotations
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Resources {



	/**
	 * Custom Post Type object
	 *
	 * @since 0.1
	 * @access public
	 * @var object $cpt The Custom Post Type object
	 */
	public $cpt;



	/**
	 * Metaboxes object
	 *
	 * @since 0.1
	 * @access public
	 * @var object $metaboxes The Metaboxes object
	 */
	public $metaboxes;



	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// include files
		$this->include_files();

		// setup globals
		$this->setup_globals();

		// register hooks
		$this->register_hooks();

	}



	/**
	 * Include files
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// include CPT class
		include_once SOF_RESOURCES_PATH . 'includes/sof-resources-cpt.php';

		// include Metaboxes class
		include_once SOF_RESOURCES_PATH . 'includes/sof-resources-metaboxes.php';

	}



	/**
	 * Set up objects
	 *
	 * @since 0.1
	 */
	public function setup_globals() {

		// init CPT object
		$this->cpt = new Spirit_Of_Football_Resources_CPT;

		// init Metaboxes object
		$this->metaboxes = new Spirit_Of_Football_Resources_Metaboxes;

	}



	/**
	 * Register Wordpress hooks
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// use translation
		add_action( 'plugins_loaded', array( $this, 'translation' ) );

		// hooks that always need to be present
		$this->cpt->register_hooks();
		$this->metaboxes->register_hooks();

	}



	/**
	 * Actions to perform on plugin activation
	 *
	 * @since 0.1
	 */
	public function activate() {

		// pass through
		$this->cpt->activate();

	}



	/**
	 * Actions to perform on plugin deactivation (NOT deletion)
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// pass through
		$this->cpt->deactivate();

	}



	/**
	 * Loads translation, if present
	 *
	 * @since 0.1
	 */
	public function translation() {

		// only use, if we have it...
		if ( function_exists( 'load_plugin_textdomain' ) ) {

			// not used, as there are no translations as yet
			load_plugin_textdomain(

				// unique name
				'sof-resources',

				// deprecated argument
				false,

				// relative path to directory containing translation files
				dirname( plugin_basename( SOF_RESOURCES_FILE ) ) . '/languages/'

			);

		}

	}



} // class Spirit_Of_Football_Resources ends



// Instantiate the class
global $sof_resources_plugin;
$sof_resources_plugin = new Spirit_Of_Football_Resources();

// activation
register_activation_hook( __FILE__, array( $sof_resources_plugin, 'activate' ) );

// deactivation
register_deactivation_hook( __FILE__, array( $sof_resources_plugin, 'deactivate' ) );



