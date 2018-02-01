<?php

namespace WPIM;

use WPIM\Marker;

class Shortcodes {

	protected $tag = 'wp_image_markers';

	public function __construct() {
		add_shortcode( $this->tag, array( $this, 'render' ) );
	}

	public function render( $args ) {

		if ( empty( $args['id'] ) ) {
			return;
		}
		
		$collection = new Marker\Collection( $args['id'] );
		
		wpim_template( 'markers', array( 'collection' => $collection ) );
	}

}
