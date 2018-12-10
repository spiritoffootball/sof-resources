<?php

/**
 * SOF Resources Custom Post Type Class
 *
 * A class that encapsulates a Custom Post Types for Resources
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Resources_CPT {



	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// nothing

	}



	/**
	 * Register WordPress hooks
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// always create post types
		add_action( 'init', array( $this, 'create_post_type' ) );

		// make sure our feedback is appropriate
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

	}




	/**
	 * Actions to perform on plugin activation
	 *
	 * @since 0.1
	 */
	public function activate() {

		// pass through
		$this->create_post_type();

		// go ahead and flush
		flush_rewrite_rules();

	}



	/**
	 * Actions to perform on plugin deactivation (NOT deletion)
	 *
	 * @since 0.1
	 */
	public function deactivate() {

		// flush rules to reset
		flush_rewrite_rules();

	}



	// #########################################################################



	/**
	 * Create our Custom Post Type
	 *
	 * @since 0.1
	 */
	public function create_post_type() {

		// only call this once
		static $registered;

		// bail if already done
		if ( $registered ) return;

		// set up the post type called "Resource"
		register_post_type( 'resource',

			array(
				'labels' => array(
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
					'not_found' =>  __( 'No matching Resource found', 'sof-resources' ),
					'not_found_in_trash' => __( 'No Resources found in Trash', 'sof-resources' ),
					'parent_item_colon' => '',
					'menu_name' => __( 'Resources', 'sof-resources' ),
				),
				'public' => true,
				'publicly_queryable' => true,
				'has_archive' => true,
				'show_ui' => true,
				'rewrite' => array(
					'slug' => 'resources',
					'with_front' => false
				),
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'show_in_nav_menus' => false,
				'menu_position' => 5,
				'exclude_from_search' => false,
				'supports' => array(
					'title',
					'editor'
				),
			)

		);

		//flush_rewrite_rules();

		// flag
		$registered = true;

	}



	/**
	 * Override messages for a custom post type
	 *
	 * @param array $messages The existing messages
	 * @return array $messages The modified messages
	 */
	public function updated_messages( $messages ) {

		// access relevant globals
		global $post, $post_ID;

		// define custom messages for our custom post type
		$messages['resource'] = array(

			// unused - messages start at index 1
			0 => '',

			// item updated
			1 => sprintf(
				__( 'Resource updated. <a href="%s">View resource</a>', 'sof-resources' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// custom fields
			2 => __( 'Custom field updated.', 'sof-resources' ),
			3 => __( 'Custom field deleted.', 'sof-resources' ),
			4 => __( 'Resource updated.', 'sof-resources' ),

			// item restored to a revision
			5 => isset( $_GET['revision'] ) ?

					// revision text
					sprintf(
						// translators: %s: date and time of the revision
						__( 'Resource restored to revision from %s', 'sof-resources' ),
						wp_post_revision_title( (int) $_GET['revision'], false )
					) :

					// no revision
					false,

			// item published
			6 => sprintf(
				__( 'Resource published. <a href="%s">View resource</a>', 'sof-resources' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// item saved
			7 => __( 'Resource saved.', 'sof-resources' ),

			// item submitted
			8 => sprintf(
				__( 'Resource submitted. <a target="_blank" href="%s">Preview resource</a>', 'sof-resources' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// item scheduled
			9 => sprintf(
				__( 'Resource scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview resource</a>', 'sof-resources' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ),
				strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// draft updated
			10 => sprintf(
				__( 'Resource draft updated. <a target="_blank" href="%s">Preview resource</a>', 'sof-resources' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			)

		);

		// --<
		return $messages;

	}



} // class Spirit_Of_Football_Resources_CPT ends



