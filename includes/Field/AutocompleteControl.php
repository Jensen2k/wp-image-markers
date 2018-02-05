<?php

namespace WPIM\Field;

class AutocompleteControl extends Control {

	private $data = array();
	private $min_length;

	public function __construct( $args = array() ) {

		$this->type = 'autocomplete';

		$this->data = isset( $args['data'] ) ? $args['data'] : array();

		$this->min_length = isset( $args['min_length'] ) ? absint( $args['min_length'] ) : 3;

		parent::__construct( $args );
	}

	public function render() {

		$output = '';

		$ajax_type = 'post_type';
		$ajax_value = array( 'post' );

		if ( !empty( $this->data ) && is_array( $this->data ) ) {
			$ajax_type = key( $this->data );
			if ( !empty( $this->data[$ajax_type] ) && is_array( $this->data[$ajax_type] ) ) {
				$ajax_value = $this->data[$ajax_type];
			}
		}

		$ajax_value = implode( ',', $ajax_value );

		$output .= sprintf( '<div class="wpim-field wpim-autocomplete" data-ajax_type="%s" data-ajax_value="%s" data-min_length="%d">', $ajax_type, $ajax_value, $this->min_length );

		if ( is_array( $this->value ) ) {
			$this->value = implode( ',', $this->value );
		}

		$output .= sprintf( '<input type="hidden" value="%s" %s/>',$this->value, implode( ' ', $this->input_attrs() ) );

		$placeholder = sprintf( esc_html__( 'Please enter %d or more characters', 'wp-image-markers' ), $this->min_length );

		if ( $this->placeholder ) {
			$placeholder = $this->placeholder;
		}

		$multiple = $this->multiple ? 'multiple' : '';

		$output .= sprintf( '<select %s placeholder="%s">', $multiple, $placeholder );
		
		if ( !empty( $this->value ) ) {

			$value = explode( ',', $this->value );
			
			foreach ( $value as $id ) {
				if ( $ajax_type == 'post_type' ) {
					
					$post = get_post( $id );
					$post_id = isset( $post->ID ) ? $post->ID : '';

					$output .= sprintf( '<option value="%s" %s>%s</option>',$post_id, selected($post_id, $id, false ), get_the_title( $post ) );
				} else if ( $ajax_type == 'taxonomy' ) {
					$term = get_term( $id );
					if ( $term ) {
						$output .= sprintf( '<option value="%s" %s>%s</option>', $term->term_id, selected( $term->term_id, $id, false ), $term->name );
					}
				}
			}
		}

		$output .= '</select></div>';
		return $output;
	}

}
