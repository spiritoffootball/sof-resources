<?php

/**
 * SOF Resources Metaboxes Class
 *
 * A class that encapsulates all Metaboxes for Resources
 *
 * @package WordPress
 * @subpackage SOF
 */
class Spirit_Of_Football_Resources_Metaboxes {



	/**
	 * Sticky Resource meta key
	 *
	 * @since 0.1
	 * @access public
	 * @var str $meta_key The meta key for sticky resources
	 */
	public $sticky_meta_key = 'resource_sticky';



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

		// exclude from SOF eV for now...
		//if ( 'sofev' == sof_get_site() ) return;

		// add meta boxes
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// intercept save
		add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );

	}




	// #########################################################################



	/**
	 * Adds meta boxes to admin screens
	 *
	 * @since 0.1
	 */
	public function add_meta_boxes() {

		// add our meta box
		add_meta_box(
			'sof_resource_options',
			__( 'Stick at top', 'sof-resources' ),
			array( $this, 'resource_box' ),
			'resource',
			'side'
		);

	}



	/**
	 * Adds a "Stick at top" meta box to Resource edit screens
	 *
	 * @since 0.1
	 * @param WP_Post $post The object for the current post/page
	 */
	public function resource_box( $post ) {

		// Use nonce for verification
		wp_nonce_field( 'sof_resource_settings', 'sof_resource_nonce' );

		// set key
		$db_key = '_' . $this->sticky_meta_key;

		// default to empty
		$val = '';

		// get value if if the custom field already has one
		$existing = get_post_meta( $post->ID, $db_key, true );
		if ( ! empty( $existing ) ) {
			$val = get_post_meta( $post->ID, $db_key, true );
		}

		// open
		echo '<p>';

		// checkbox
		echo '<input id="' . $this->sticky_meta_key . '" name="' . $this->sticky_meta_key . '" value="1" type="checkbox" ' . (($val == '1') ? ' checked="checked"' : '') . '/>';

		// construct label
		echo '<strong><label for="' . $this->sticky_meta_key . '">' . __( 'Stick at top of lists', 'sof-resources' ) . '</label></strong>';

		// close
		echo '</p>';

	}



	/**
	 * Stores our additional params
	 *
	 * @since 0.1
	 * @param integer $post_id the ID of the post (or revision)
	 * @param integer $post the post object
	 */
	public function save_post( $post_id, $post ) {

		// we don't use post_id because we're not interested in revisions

		// store our page meta data
		$result = $this->_save_page_meta( $post );

	}



	// #########################################################################



	/**
	 * When a page is saved, this also saves the options
	 *
	 * @since 0.1
	 * @param WP_Post $post_obj The object for the post (or revision)
	 */
	private function _save_page_meta( $post_obj ) {

		// if no post, kick out
		if ( ! $post_obj ) return;

		// authenticate
		$nonce = isset( $_POST['sof_resource_nonce'] ) ? $_POST['sof_resource_nonce'] : '';
		if ( ! wp_verify_nonce( $nonce, 'sof_resource_settings' ) ) return;

		// is this an auto save routine?
		if ( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) return;

		// Check permissions
		if ( ! current_user_can( 'edit_page', $post_obj->ID ) ) return;

		// check for revision
		if ( $post_obj->post_type == 'revision' ) {

			// get parent
			if ( $post_obj->post_parent != 0 ) {
				$post = get_post( $post_obj->post_parent );
			} else {
				$post = $post_obj;
			}

		} else {
			$post = $post_obj;
		}

		// bail if not resource post type
		if ( $post->post_type == 'resource' ) return;

		// ---------------------------------------------------------------------
		// okay, we're through...
		// ---------------------------------------------------------------------

		// define key
		$db_key = '_' . $this->sticky_meta_key;

		// get sticky value
		$is_sticky = ( isset( $_POST[$this->sticky_meta_key] ) ) ? '1' : '0';

		// save for this post
		$this->_save_meta( $post, $db_key, $is_sticky );

		// delete this meta value from all other resources because only one
		// resource can be sticky
		if ( $is_sticky == '1' ) {

			// get all resources
			$resources = get_posts( array(
				'post_type' => 'resource'
			) );

			// if we have any
			if ( count( $resources ) > 0 ) {

				// loop and save, excluding current post
				foreach( $resources AS $resource ) {
					if ( $post->ID != $resource->ID ) {
						$this->_save_meta( $post, $db_key, '0' );
					}
				}

			}

		}

	}



	/**
	 * Utility to automate meta data saving
	 *
	 * @since 0.1
	 * @param WP_Post $post_obj The WordPress post object
	 * @param string $key The meta key
	 * @param mixed $data The data to be saved
	 * @return mixed $data The data that was saved
	 */
	private function _save_meta( $post, $key, $data = '' ) {

		// if the custom field already has a value...
		$existing = get_post_meta( $post->ID, $key, true );
		if ( ! empty( $existing ) ) {

			// update the data
			update_post_meta( $post->ID, $key, $data );

		} else {

			// add the data
			add_post_meta( $post->ID, $key, $data );

		}

		// --<
		return $data;

	}




} // class Spirit_Of_Football_Resources_Metaboxes ends



