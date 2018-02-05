<?php

namespace WPIM\Admin;

class PostType {

	public function __construct() {

		add_action( 'init', array( $this, 'createPostType' ) );
		add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
		add_action( 'save_post_wp_image_markers', array( $this, 'savePost' ), 10, 2 );
	}

	public function createPostType() {

		register_post_type( 'wp_image_markers', array(
			'labels' => array(
				'name' => esc_html__( 'Image Markers', 'wp-image-markers' ),
				'singular_name' => esc_html__( 'WP Image Markers', 'wp-image-markers' ),
				'edit_item' => esc_html__( 'Edit Image Marker', 'wp-image-markers' ),
				'add_new_item' => esc_html__( 'Add new Image Marker', 'wp-image-markers' )
			),
			'menu_icon' => 'dashicons-location-alt',
			'public' => false,
			'has_archive' => false,
			'rewrite' => false,
			'query_var' => false,
			'exclude_from_search' => false,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'supports' => array( 'title' )
				)
		);
	}

	public function addMetaBox() {
		add_meta_box( 'wp_image_markers_content', esc_html__( 'Image Markers', 'wp-image-markers' ), array( $this, 'metaBoxContent' ), 'wp_image_markers', 'normal', 'high' );
	}

	public function metaBoxContent( $post ) {

		wpim_template( 'admin/content', array( 'post' => $post ) );
	}

	public function savePost( $post_id, $post ) {

		if ( empty( $_POST['_wpim_nonce'] ) ) {
			return;
		}

		if ( !wp_verify_nonce( $_POST['_wpim_nonce'], 'wpim_markers' ) ) {
			wp_die( esc_html__( 'Security is valid.', 'wp-image-markers' ) );
		}

		if ( !current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You have no permission.', 'wp-image-markers' ) );
		}

		$value = wp_kses_post( $_POST['_wpim_markers'] );

		update_post_meta( $post_id, '_wpim_markers', $value );

		$collection = new \WPIM\Marker\Collection( $post_id, stripslashes( $value ) );

		$css = $collection->renderCss();

		$arrCss = get_option( 'wpim_css', array() );

		if ( !is_array( $arrCss ) ) {
			$arrCss = array();
		}

		$arrCss[$post_id] = $css;

		update_option( 'wpim_css', $arrCss );
	}

}
