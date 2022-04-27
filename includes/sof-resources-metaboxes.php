<?php
/**
 * Metaboxes Class.
 *
 * Handles Metaboxes for Resources.
 *
 * @since 0.1
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
	 * @var str $meta_key The meta key for the sticky Resource.
	 */
	public $sticky_meta_key = 'resource_sticky';

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Nothing.

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

	// -------------------------------------------------------------------------

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
		$val = '';

		// Get value if if the custom field already has one.
		$existing = get_post_meta( $post->ID, $db_key, true );
		if ( ! empty( $existing ) ) {
			$val = get_post_meta( $post->ID, $db_key, true );
		}

		// Open.
		echo '<p>';

		// Checkbox.
		echo '<input id="' . $this->sticky_meta_key . '" name="' . $this->sticky_meta_key . '" value="1" type="checkbox" ' . ( ( $val == '1' ) ? ' checked="checked"' : '' ) . '/>';

		// Construct label.
		echo '<strong><label for="' . $this->sticky_meta_key . '">' . __( 'Stick at top of lists', 'sof-resources' ) . '</label></strong>';

		// Close.
		echo '</p>';

	}

	/**
	 * Stores our additional params.
	 *
	 * @since 0.1
	 *
	 * @param integer $post_id The ID of the post or revision.
	 * @param integer $post The post object.
	 */
	public function save_post( $post_id, $post ) {

		// We don't use post_id because we're not interested in revisions.

		// Store our page meta data.
		$result = $this->save_page_meta( $post );

	}

	// -------------------------------------------------------------------------

	/**
	 * When a page is saved, this also saves the options.
	 *
	 * @since 0.1
	 *
	 * @param WP_Post $post_obj The object for the post or revision.
	 */
	private function save_page_meta( $post_obj ) {

		// Bail if no post.
		if ( ! $post_obj ) {
			return;
		}

		// Authenticate.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$nonce = isset( $_POST['sof_resource_nonce'] ) ? wp_unslash( $_POST['sof_resource_nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'sof_resource_settings' ) ) {
			return;
		}

		// Is this an auto save routine?
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions.
		if ( ! current_user_can( 'edit_page', $post_obj->ID ) ) {
			return;
		}

		// Check for revision.
		if ( $post_obj->post_type == 'revision' ) {

			// Get parent.
			if ( $post_obj->post_parent != 0 ) {
				$post = get_post( $post_obj->post_parent );
			} else {
				$post = $post_obj;
			}

		} else {
			$post = $post_obj;
		}

		// Bail if not resource post type.
		if ( $post->post_type == 'resource' ) {
			return;
		}

		// ---------------------------------------------------------------------
		// Okay, we're through...
		// ---------------------------------------------------------------------

		// Define prefixed key.
		$db_key = '_' . $this->sticky_meta_key;

		// Get sticky value.
		$is_sticky = ( isset( $_POST[ $this->sticky_meta_key ] ) ) ? '1' : '0';

		// Save for this post.
		$this->save_meta( $post, $db_key, $is_sticky );

		// Delete this meta value from all other resources because only one Resource can be sticky.
		if ( $is_sticky == '1' ) {

			// Get all Resources.
			$resources = get_posts( [
				'post_type' => 'resource',
			] );

			// If we have any.
			if ( count( $resources ) > 0 ) {

				// Loop and save, excluding current post.
				foreach ( $resources as $resource ) {
					if ( $post->ID != $resource->ID ) {
						$this->save_meta( $post, $db_key, '0' );
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
	 * @param WP_Post $post The WordPress post object.
	 * @param string $key The meta key.
	 * @param mixed $data The data to be saved.
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
