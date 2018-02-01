<?php

namespace WPIM\Field;

class RadioImageControl extends Control {

	public function __construct( $args = array() ) {

		$this->type = 'radio_image';

		parent::__construct( $args );
	}

	public function render() {

		$output = '';

		if ( is_array( $this->options ) ) {


			$image_size = '';

			if ( isset( $this->input_attrs['image_size'][0] ) ) {

				$height = $this->input_attrs['image_size'][0];

				if ( isset( $this->input_attrs['image_size'][1] ) ) {
					$height = $this->input_attrs['image_size'][1];
				}

				$image_size = sprintf( 'style="width:%s;height:%s"', $this->input_attrs['image_size'][0], $height );
			}

			$type = !empty( $this->input_attrs['inline'] ) ? 'inline' : 'list';

			$output .= '<div class="wpim-field wpim-radio_image wpim-radio_image--' . esc_attr( $type ) . '">';
			$output .= '<ul>';

			foreach ( $this->options as $radio_key => $args ) {

				$radio_url = $args;
				$group = '';
				$title = '';

				if ( is_array( $args ) ) {
					$radio_url = $args['url'];
					if ( isset( $args['group'] ) ) {
						$group = implode( ',', $args['group'] );
					}
					if ( isset( $args['title'] ) ) {
						$title = $args['title'];
					}
				}

				$checked = $radio_key == $this->value ? 'checked' : '';

				$output .= sprintf( '<li data-group="%5$s"><label><input %1$s type="radio" value="%2$s"/><span><img title="%6$s" alt="%2$s" src="%3$s" %4$s/></span></label></li>', $checked, $radio_key, $radio_url, $image_size, $group, $title );
			}

			$output .= '</ul>';

			if ( isset( $this->input_attrs['image_size'] ) ) {
				unset( $this->input_attrs['image_size'] );
			}

			if ( isset( $this->input_attrs['inline'] ) ) {
				unset( $this->input_attrs['inline'] );
			}

			$output .= sprintf( '<input type="hidden" value="%s" %s/></div>', $this->value, implode( ' ', $this->input_attrs() ) );
		}

		return $output;
	}

}
