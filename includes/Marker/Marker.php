<?php

namespace WPIM\Marker;

class Marker {

	/**
	 * Stores event data.
	 *
	 * @var array
	 */
	protected $data = array(
		'id' => '',
		'valueType' => '',
		'value' => null,
		'custom_hover' => 0,
		'infowindow' => array(),
	);

	/**
	 * @var Css Class
	 */
	public $css;

	/**
	 * @var InfoWindow Class
	 */
	public $infowindow;

	public function __construct( $args = array() ) {

		$this->data = wp_parse_args( $args, $this->data );
		$this->infowindow = new InfoWindow( $this->data['infowindow'] );
		$this->css = new Css( $this->data );
	}

	public function __set( $name, $value ) {

		if ( isset( $this->data[$name] ) ) {
			$this->data[$name] = $value;
		}
	}

	public function __get( $name ) {
		return isset( $this->data[$name] ) ? $this->data[$name] : '';
	}

	/**
	 * Get event id
	 * @return string
	 */
	public function get_id() {
		return $this->__get( 'id' );
	}

	public function get_type() {
		return $this->__get( 'valueType' );
	}

	public function get_value() {
		return $this->__get( 'value' );
	}

	public function get_custom_hover() {
		return absint( $this->__get( 'custom_hover' ) );
	}

	public function get_value_html() {

		$html = sprintf( '<i>%s</i>', $this->get_value() );

		if ( $this->get_type() == 'image' ) {

			$attachment = explode( '|', $this->get_value() );

			$html = wp_get_attachment_image( $attachment[0], 'thumbnail', false, array( 'class' => 'wpim-icon-image' ) );

			if ( $this->css->image_hover() ) {
				$html .= wp_get_attachment_image( $this->css->image_hover(), 'thumbnail', false, array( 'class' => 'wpim-icon-image__hover' ) );
			}
		} else if ( $this->get_type() == 'icon' ) {
			$html = sprintf( '<i class="%s"></i>', $this->get_value() );
		}

		return $html;
	}

	/**
	 * Get Css attribute
	 * @return array
	 */
	public function parseCss() {

		$css = array(
			'width' => $this->sanitizeSize( $this->css->size( 'width' ) ),
			'height' => $this->sanitizeSize( $this->css->size( 'height' ) ),
		);

		$borderStyle = $this->css->border( 'style' );
		$css['border-radius'] = $this->css->radius() . '%';
		$css['border-width'] = $this->sanitizeSize( $this->css->border( 'width' ) );
		$css['border-style'] = $borderStyle;
		$css['border-color'] = $this->css->border( 'color' );
		$css['transform'] = sprintf( 'rotate(%ddeg)', $this->css->rotate() );

		if ( $borderStyle == 'zigzag' ) {
			$css['border-width'] = 0;
			$css['border-style'] = 'none';
		} elseif ( in_array( $borderStyle, $this->getBorderArrows() ) ) {
			$css['border-style'] = 'solid';
		}

		if ( $this->get_type() != 'image' ) {

			$css['background-color'] = $this->css->background( 'color' );

			$css['color'] = $this->css->font( 'color' );
			$css['font-size'] = $this->sanitizeSize( $this->css->font( 'size' ) );
			$css['line-height'] = $this->sanitizeSize( $this->css->size( 'line-height' ) );
		}

		if ( $this->css->shadow( 'color' ) ) {
			$css['box-shadow'] = sprintf( '%s %s %s %s', $this->sanitizeSize( $this->css->shadow( 'h' ) ), $this->sanitizeSize( $this->css->shadow( 'v' ) ), $this->sanitizeSize( $this->css->shadow( 'blur' ) ), $this->css->shadow( 'color' ) );
		}

		return apply_filters( 'wpim\maker\parse_css', $css, $this );
	}

	/**
	 * Get css rendered to string
	 * @return string
	 */
	public function renderCss( $prop = '' ) {

		$attributes = $this->parseCss();

		if ( !$prop ) {
			$prop = '.' . $this->get_id();
		}

		$css = sprintf( '%s{top:%s;left:%s}', $prop, $this->sanitizeSize( $this->css->position( 'top' ) ), $this->sanitizeSize( $this->css->position( 'left' ) ) );

		$css .= sprintf( '%s .wpim-marker__icon{', $prop );

		foreach ( $attributes as $key => $value ) {
			$css .= sprintf( '%s:%s;', $key, $value );
		}

		$css .= '}';

		if ( $this->get_custom_hover() ) {

			$css .= sprintf( '%s:hover .wpim-marker__icon{', $prop );

			$css .= sprintf( 'border-color:%s;', $this->css->border( 'color_hover' ) );

			if ( $this->get_type() != 'image' ) {
				$bg_color = $this->css->background( 'color_hover' );
				$text_color = sanitize_hex_color( $this->css->font( 'color_hover' ) );
				$css .= sprintf( 'background-color:%s;color:%s;', $bg_color, $text_color );
			}

			$css .= '}';
		}

		$css .= $this->renderCssBorderArrow( $prop );

		return apply_filters( 'wpim\maker\render_css', $css, $prop, $attributes, $this->get_id() );
	}

	/**
	 * @return array Arrow values
	 */
	private function getBorderArrows() {
		return array( 'solid-arrow-top', 'solid-arrow-right', 'solid-arrow-bottom', 'solid-arrow-left' );
	}

	private function renderCssBorderArrow( $prop = '' ) {
		$css = '';
		if ( in_array( $this->css->border( 'style' ), $this->getBorderArrows() ) ) {
		
			$size = intval( $this->css->border( 'width' ) ) * 3;
			$color = $this->css->border( 'color' );
			$color_hover = $this->css->border( 'color_hover' );

			switch ( $this->css->border( 'style' ) ) {

				case'solid-arrow-bottom':

					$css .= sprintf( '%s .wpim-marker__icon:after{', $prop );
					$css .= sprintf( 'border-left: %1$dpx solid transparent;
								border-right: %1$dpx solid transparent;
								border-top: %1$dpx solid %2$s;', $size, $color );
					$css .= "left: calc(50% - {$size}px);top: 100%;}";

					if ( $this->get_custom_hover() ) {
						$css .= sprintf( '%s:hover .wpim-marker__icon:after{', $prop );
						$css .= sprintf( 'border-top-color: %s;}', $color_hover );
					}

					break;

				case'solid-arrow-top':

					$css .= sprintf( '%s .wpim-marker__icon:after{', $prop );
					$css .= sprintf( 'border-left: %1$dpx solid transparent;
								border-right: %1$dpx solid transparent;
								border-bottom: %1$dpx solid %2$s;', $size, $color );
					$css .= "left: calc(50% - {$size}px);bottom: 100%;}";

					if ( $this->get_custom_hover() ) {
						$css .= sprintf( '%s:hover .wpim-marker__icon:after{', $prop );
						$css .= sprintf( 'border-bottom-color: %s;}', $color_hover );
					}

					break;

				case'solid-arrow-left':

					$css .= sprintf( '%s .wpim-marker__icon:after{', $prop );
					$css .= sprintf( 'border-top: %1$dpx solid transparent;
								border-bottom: %1$dpx solid transparent;
								border-right: %1$dpx solid %2$s;', $size, $color );
					$css .= "right: 100%;top: calc(50% - {$size}px);} ";

					if ( $this->get_custom_hover() ) {
						$css .= sprintf( '%s:hover .wpim-marker__icon:after{', $prop );
						$css .= sprintf( 'border-right-color: %s;}', $color_hover );
					}
					break;

				case'solid-arrow-right':

					$css .= sprintf( '%s .wpim-marker__icon:after{', $prop );
					$css .= sprintf( 'border-top: %1$dpx solid transparent;
								border-bottom: %1$dpx solid transparent;
								border-left: %1$dpx solid %2$s;', $size, $color );
					$css .= "left: 100%;top: calc(50% - {$size}px);}";

					if ( $this->get_custom_hover() ) {
						$css .= sprintf( '%s:hover .wpim-marker__icon:after{', $prop );
						$css .= sprintf( 'border-left-color: %s;}', $color_hover );
					}

					break;
			}
		}

		return $css;
	}

	/**
	 * Sanitize css size
	 * Allowed metrics: http://www.w3schools.com/cssref/css_units.asp
	 * 
	 * @param string|int $value Css Size
	 * @return string
	 */
	public function sanitizeSize( $value ) {
		$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
		$regexr = preg_match( $pattern, $value, $matches );
		$value = isset( $matches[1] ) ? (float) $matches[1] : (float) $value;
		$unit = isset( $matches[2] ) ? $matches[2] : 'px';
		$value = $value . $unit;

		return $value;
	}

}
