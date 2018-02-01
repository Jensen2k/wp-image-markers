<?php

namespace WPIM\Marker;

class Css {

	protected $data = array(
		'image_hover' => '',
		'background' => array(),
		'size' => array(),
		'position' => array(),
		'border' => array(),
		'shadow' => array(),
		'radius' => '',
		'font' => array(),
		'rotate' => 0
	);

	public function __construct( $args ) {
		$this->data = wp_parse_args( $args, $this->data );
	}

	/**
	 * Magic method get
	 * @return mixed
	 */
	public function __get( $name ) {
		return isset( $this->data[$name] ) ? $this->data[$name] : '';
	}

	public function background( $key = '' ) {
		$arr = wp_parse_args( $this->__get( 'background' ), array(
			'color' => '',
			'color_hover',
				) );

		if ( $key ) {
			return isset( $arr[$key] ) ? $arr[$key] : '';
		}

		return $arr;
	}

	public function size( $key = '' ) {
		$args = wp_parse_args( $this->__get( 'size' ), array(
			'width' => 30,
			'height' => 30,
			'line-height'=>30
				) );

		$arr = array(
			'width' => $args['width'],
			'height' => $args['height'],
			'line-height'=>$args['line-height']
		);

		if ( $key ) {
			return isset( $arr[$key] ) ? $arr[$key] : '';
		}

		return $arr;
	}

	public function position( $key = '' ) {
		$args = wp_parse_args( $this->__get( 'position' ), array(
			'left' => 50,
			'top' => 50,
				) );

		$arr = array(
			'left' => $args['left'],
			'top' => $args['top']
		);


		if ( $key ) {
			return isset( $arr[$key] ) ? $arr[$key] : '';
		}

		return $arr;
	}

	public function border( $key = '' ) {
		$args = wp_parse_args( $this->__get( 'border' ), array(
			'width' => '1px',
			'style' => 'solid',
			'color' => '',
			'color_hover' => ''
				) );

		$arr = array(
			'width' => $args['width'],
			'style' => $args['style'],
			'color' => $args['color'],
			'color_hover' => $args['color_hover']
		);

		if ( $key ) {
			return isset( $arr[$key] ) ? $arr[$key] : '';
		}

		return $arr;
	}

	public function shadow( $key = '' ) {
		$args = wp_parse_args( $this->__get( 'shadow' ), array(
			'h' => '',
			'v' => '',
			'blur' => '',
			'color' => ''
				) );

		$arr = array(
			'h' => $args['h'],
			'v' => $args['v'],
			'blur' => $args['blur'],
			'color' => $args['color']
		);

		if ( $key ) {
			return isset( $arr[$key] ) ? $arr[$key] : '';
		}

		return $arr;
	}

	public function radius() {
		return intval( $this->__get( 'radius' ) );
	}

	public function font( $key = '' ) {

		$args = wp_parse_args( $this->__get( 'font' ), array(
			'size' => '',
			'color' => '',
			'color_hover' => '',
			'weight' => 'bold',
			'style'=>'normal'
				) );

		$arr = array(
			'size' => $args['size'],
			'color' => $args['color'],
			'color_hover' => $args['color_hover'],
			'weight' => $args['weight'],
			'style' => $args['style'],
		);

		if ( $key ) {
			return isset( $arr[$key] ) ? $arr[$key] : '';
		}

		return $arr;
	}

	/**
	 * Get image hover id
	 * @return int
	 */
	public function image_hover() {
		$attachment = $this->__get( 'image_hover' );
		if ( $attachment ) {
			$attachment = explode( '|', $attachment );
			return $attachment[0];
		}

		return 0;
	}

	public function rotate() {
		return intval( $this->__get( 'rotate' ) );
	}

}
