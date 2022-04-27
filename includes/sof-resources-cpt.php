<?php
/**
 * Custom Post Type Class.
 *
 * Handles the Custom Post Type for Quotes.
 *
 * @since 0.1
 *
 * @package Spirit_Of_Football_Quotes
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Custom Post Type Class.
 *
 * A class that encapsulates a Custom Post Types for Resources.
 *
 * @since 0.1
 */
class Spirit_Of_Football_Resources_CPT {

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

		// Always create post types.
		add_action( 'init', [ $this, 'create_post_type' ] );

		// Make sure our feedback is appropriate.
		add_filter( 'post_updated_messages', [ $this, 'updated_messages' ] );

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {

		// Pass through.
		$this->create_post_type();

		// Go ahead and flush.
		flush_rewrite_rules();

	}

	/**
	 * Actions to perform on plugin deactivation (NOT deletion)
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}

	// -------------------------------------------------------------------------

	/**
	 * Create our Custom Post Type.
	 *
	 * @since 0.1
	 */
	public function create_post_type() {

		// Only call this once.
		static $registered;

		// Bail if already done.
		if ( $registered ) {
			return;
		}

		// Set up the post type called "Resource".
		register_post_type( 'resource',
			[
				'labels' => [
					'name' => __( 'Resources', 'sof-resources' ),
					'singular_name' => __( 'Resource', 'sof-resources' ),
					'add_new' => _x( 'Add New', 'resource', 'sof-resources' ),
					'add_new_item' => __( 'Add New Resource', 'sof-resources' ),
					'edit_item' => __( 'Edit Resource', 'sof-resources' ),
					'new_item' => __( 'New Resource', 'sof-resources' ),
					'all_items' => __( 'All Resources', 'sof-resources' ),
					'view_item' => __( 'View Resource', 'sof-resources' ),
					'item_published' => __( 'Resource published.', 'sof-resources' ),
					'item_published_privately' => __( 'Resource published privately.', 'sof-resources' ),
					'item_reverted_to_draft' => __( 'Resource reverted to draft.', 'sof-resources' ),
					'item_scheduled' => __( 'Resource scheduled.', 'sof-resources' ),
					'item_updated' => __( 'Resource updated.', 'sof-resources' ),
					'search_items' => __( 'Search Resources', 'sof-resources' ),
					'not_found' => __( 'No matching Resource found', 'sof-resources' ),
					'not_found_in_trash' => __( 'No Resources found in Trash', 'sof-resources' ),
					'parent_item_colon' => '',
					'menu_name' => __( 'Resources', 'sof-resources' ),
				],
				'public' => true,
				'publicly_queryable' => true,
				'has_archive' => true,
				'show_ui' => true,
				'rewrite' => [
					'slug' => 'resources',
					'with_front' => false,
				],
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'show_in_nav_menus' => false,
				'menu_position' => 5,
				'exclude_from_search' => false,
				'supports' => [
					'title',
					'editor',
				],
			]
		);

		// Flag.
		$registered = true;

	}

	/**
	 * Override messages for a custom post type.
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	public function updated_messages( $messages ) {

		// Access relevant globals.
		global $post, $post_ID;

		// Define custom messages for our custom post type.
		$messages['resource'] = [

			// Unused - messages start at index 1.
			0 => '',

			// Item updated.
			1 => sprintf(
				/* translators: %s: Post permalink URL. */
				__( 'Resource updated. <a href="%s">View resource</a>', 'sof-resources' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2 => __( 'Custom field updated.', 'sof-resources' ),
			3 => __( 'Custom field deleted.', 'sof-resources' ),
			4 => __( 'Resource updated.', 'sof-resources' ),

			// Item restored to a revision.
			5 => isset( $_GET['revision'] ) ?

				// Revision text.
				sprintf(
					/* translators: %s: Title of the revision. */
					__( 'Resource restored to revision from %s', 'sof-resources' ),
					wp_post_revision_title( (int) $_GET['revision'], false )
				) :

				// No revision.
				false,

			// Item published.
			6 => sprintf(
				/* translators: %s: Post permalink URL. */
				__( 'Resource published. <a href="%s">View resource</a>', 'sof-resources' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7 => __( 'Resource saved.', 'sof-resources' ),

			// Item submitted.
			8 => sprintf(
				/* translators: %s: Post preview URL. */
				__( 'Resource submitted. <a target="_blank" href="%s">Preview resource</a>', 'sof-resources' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9 => sprintf(
				/* translators: 1: Publish box date format, see http://php.net/date, 2: Post date, 3: Post permalink. */
				__( 'Resource scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview resource</a>', 'sof-resources' ),
				/* translators: Publish box date format, see http://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'sof-resources' ),
				strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				/* translators: %s: Post preview URL. */
				__( 'Resource draft updated. <a target="_blank" href="%s">Preview resource</a>', 'sof-resources' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

		];

		// --<
		return $messages;

	}

}
