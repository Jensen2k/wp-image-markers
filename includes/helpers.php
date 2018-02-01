<?php

/**
 * Get template file path
 * @since 1.0
 * @return string
 */
function wpim_template_path($slug){
		
	$path = apply_filters( 'wpim\template_path', get_template_directory() . '/wp-image-markers' ) . '/' . $slug . '.php';

	if ( !file_exists( $path ) ) {
		$path = WPIM_DIR . 'templates/' . $slug . '.php';
	}
	
	return $path;
}

/**
 * Template file
 * 
 * @param string $slug
 * @param array $data
 * @since 1.0
 */
function wpim_template( $slug, $data = array() ) {

	if ( is_array( $data ) ) {
		extract( $data );
	}
	
	include wpim_template_path($slug);
}

/**
 * Sanitize plugin list
 * 
 * @param string $value
 * @return array | bool
 */
function wpim_sanitize_array( $value ) {

	if ( empty( $value ) ) {
		return array();
	}

	if ( is_string( $value ) ) {
		$value = array( $value );
	}

	return array_map( function($val) {
		return sanitize_text_field( $val );
	}, $value );
}

/**
 * Parse string like "title:Wpim is useful|author:ThemesPond" to array('title' => 'Wpim is useful', 'author' => 'ThemesPond')
 *
 * @param $value
 * @param array $default
 *
 * @since 1.0.0
 * @return array
 */
function wpim_parse_multi_attribute( $value, $default = array() ) {
	$result = $default;
	$params_pairs = explode( '|', $value );
	if ( !empty( $params_pairs ) ) {
		foreach ( $params_pairs as $pair ) {
			$param = preg_split( '/\:/', $pair );
			if ( !empty( $param[0] ) && isset( $param[1] ) ) {
				$result[$param[0]] = rawurldecode( $param[1] );
			}
		}
	}

	return $result;
}

/**
 * Build Link from string
 * 
 * @param string $value
 *
 * @since 1.0.0
 * @return array
 */
function wpim_build_link( $value ) {
	return wpim_parse_multi_attribute( $value, array( 'url' => '', 'title' => '', 'target' => '', 'rel' => '' ) );
}

/**
 * Print link editor template
 * Link field need a hidden textarea to work
 * 
 * @since 1.0.0
 * @return void
 */
function wpim_link_editor_hidden() {
	echo '<textarea id="content" class="hide hidden"></textarea>';
	require_once ABSPATH . "wp-includes/class-wp-editor.php";
	_WP_Editors::wp_link_dialog();
}



/**
 * Autocomplete ajax post type
 *
 * @since 1.0.0
 * @return void
 */
function wpim_ajax_search_posts() {

	$s = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
	$post_types = !empty( $_GET['types'] ) ? explode( ',', $_GET['types'] ) : array( 'post' );

	$posts = get_posts( array(
		'posts_per_page' => 20,
		'post_type' => $post_types,
		'post_status' => 'publish',
		's' => $s
			) );

	$result = array();

	foreach ( $posts as $post ) {
		$result[] = array(
			'value' => $post->ID,
			'label' => $post->post_title,
		);
	}

	wp_send_json( $result );
}

/**
 * Autocomplete ajax taxonomy
 *
 * @since 1.0.0
 * @return void
 */
function wpim_ajax_search_taxonomies() {

	$s = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';

	$types = !empty( $_GET['types'] ) ? explode( ',', $_GET['types'] ) : array( 'category' );

	$args['taxonomy'] = $types;
	$args['hide_empty'] = false;
	$args['name__like'] = $s;


	$terms = get_terms( $args );

	$result = array();

	foreach ( $terms as $term ) {
		$result[] = array(
			'value' => $term->term_id,
			'label' => $term->name,
		);
	}

	wp_send_json( $result );
}

add_action( 'wp_ajax_wpim_autocomplete_post_type', 'wpim_ajax_search_posts' );
add_action( 'wp_ajax_wpim_autocomplete_taxonomy', 'wpim_ajax_search_taxonomies' );
