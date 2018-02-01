<?php

namespace WPIM\Marker;

class Collection {

	private $id = 0;
	private $image = array();
	private $markers = array();
	
	/**
	 * @param int $id ID Post
	 * @param string $value Meta value
	 */
	public function __construct( $id = 0,$value='' ) {

		$this->id = $id;
		
		if(!$value){
			$value = get_post_meta( $this->id, '_wpim_markers', true );
		}
		
		if ( $value ) {
		
			$data = wp_parse_args( json_decode( $value, true ), array( 'image' => array(), 'markers' => array() ) );
		
			$this->image = $data['image'];
			$this->markers = $data['markers'];
		}
		
	}
	
	public function getId(){
		return $this->id;
	}

	public function getImage() {
		return $this->image;
	}

	public function getMarkers() {
		return $this->markers;
	}

	public function renderCss() {

		$css = '';
		$markers = $this->getMarkers();
		
		foreach ( $markers as $marker ) {
			$marker = new Marker( $marker );
			$css .= $marker->renderCss( sprintf( '.wpim-%s__%s', $this->id, $marker->get_id() ));
		}
		
		return $css;
	}
	
}
