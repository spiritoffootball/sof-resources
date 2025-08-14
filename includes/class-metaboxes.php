<?php
/**
 * Metaboxes Class.
 *
 * Handles Metaboxes for Resources.
 *
 * @package Spirit_Of_Football_Resources
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Metaboxes Class.
 *
 * A class that encapsulates all Metaboxes for Resources.
 *
 * @since 0.1
 */
class Spirit_Of_Football_Resources_Metaboxes {

	/**
	 * Sticky Resource meta key
	 *
	 * @since 0.1
	 * @access public
	 * @var string
	 */
	public $sticky_meta_key = 'resource_sticky';

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Init when this plugin is loaded.
		add_action( 'sof_resources/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialise this object.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Register hooks.
		$this->register_hooks();

	}

	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Add meta boxes.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		// Intercept save.
		add_action( 'save_post', [ $this, 'save_post' ], 1, 2 );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Adds meta boxes to admin screens.
	 *
	 * @since 0.1
	 */
	public function add_meta_boxes() {

		// Add our meta box.
		add_meta_box(
			'sof_resource_options',
			__( 'Stick at top', 'sof-resources' ),
			[ $this, 'resource_box' ],
			'resource',
			'side'
		);

	}

	/**
	 * Adds a "Stick at top" meta box to Resource edit screens.
	 *
	 * @since 0.1
	 *
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function resource_box( $post ) {

		// Use nonce for verification.
		wp_nonce_field( 'sof_resource_settings', 'sof_resource_nonce' );

		// Set key.
		$db_key = '_' . $this->sticky_meta_key;

		// Default to empty.
		$checked = false;

		// Get value if if the custom field has one.
		$existing = get_post_meta( $post->ID, $db_key, true );
		if ( ! empty( $existing ) ) {
			$checked = true;
		}

		// Open.
		echo '<p>';

		// Checkbox.
		echo '<input type="checkbox" id="' . esc_attr( $this->sticky_meta_key ) . '" name="' . esc_attr( $this->sticky_meta_key ) . '" value="1" ' . checked( $checked, true, false ) . '/>';

		// Construct label.
		echo '<strong><label for="' . esc_attr( $this->sticky_meta_key ) . '">' . esc_html__( 'Stick at top of lists', 'sof-resources' ) . '</label></strong>';

		// Close.
		echo '</p>';

	}

	/**
	 * Stores our additional params.
	 *
	 * @since 0.1
	 *
	 * @param int     $post_id The ID of the WordPress Post or revision.
	 * @param WP_POst $post The WordPress Post object.
	 */
	public function save_post( $post_id, $post ) {

		// Bail if there's no Post object.
		if ( ! $post ) {
			return;
		}

		// Bail if this is an autosave.
		if ( wp_is_post_autosave( $post ) ) {
			return;
		}

		// Check for revision.
		$parent_id = wp_is_post_revision( $post );
		if ( $parent_id ) {
			$post = get_post( $parent_id );
		}

		// Bail if not the post type we want.
		if ( 'resource' !== $post->post_type ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		// Authenticate.
		$nonce = isset( $_POST['sof_resource_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['sof_resource_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'sof_resource_settings' ) ) {
			return;
		}

		// Define prefixed key.
		$db_key = '_' . $this->sticky_meta_key;

		// Get sticky value.
		$is_sticky = isset( $_POST[ $this->sticky_meta_key ] ) ? '1' : '0';

		// Save for this post.
		$this->save_meta( $post, $db_key, $is_sticky );

		// Delete this meta value from all other resources because only one Resource can be sticky.
		if ( 1 === (int) $is_sticky ) {

			// Build query args.
			$args = [
				'post_type' => 'resource',
			];

			// Get all Resources.
			$resources = get_posts( $args );

			// If we have any.
			if ( count( $resources ) > 0 ) {

				// Loop and save, excluding current post.
				foreach ( $resources as $resource ) {
					if ( (int) $post->ID !== (int) $resource->ID ) {
						$this->save_meta( $resource, $db_key, '0' );
					}
				}

			}

		}

	}

	/**
	 * Utility to automate meta data saving.
	 *
	 * @since 0.1
	 *
	 * @param WP_Post $post The WordPress Post object.
	 * @param string  $key The meta key.
	 * @param mixed   $data The data to be saved.
	 * @return mixed $data The data that was saved.
	 */
	private function save_meta( $post, $key, $data = '' ) {

		// If the custom field already has a value.
		$existing = get_post_meta( $post->ID, $key, true );
		if ( ! empty( $existing ) ) {

			// Update the data.
			update_post_meta( $post->ID, $key, $data );

		} else {

			// Add the data.
			add_post_meta( $post->ID, $key, $data );

		}

		// --<
		return $data;

	}

}
