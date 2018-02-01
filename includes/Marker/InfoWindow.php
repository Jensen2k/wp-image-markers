<?php

namespace WPIM\Marker;

class InfoWindow{
	
	protected $data = array(
		'datasource'=>'',
		'content'=>'',
		'event'=>'',
		'position'=>''
	);
	

	public function __construct($windowinfo=array()) {
		$this->data = wp_parse_args($windowinfo, $this->data);
		
	}
	
	/**
	 * Magic method get
	 * @return mixed
	 */
	public function __get( $name ) {
		return isset( $this->data[ $name ] ) ? $this->data[ $name ] : '';
	}
	
	/**
	 * Get datasource
	 * @return string
	 */
	public function get_datasource(){
		return $this->__get('datasource');
	}
	
	/**
	 * Get text content
	 * @return string
	 */
	public function get_content(){
		return $this->__get('content');
	}
	
	/**
	 * Get style
	 * @return string
	 */
	public function get_position(){
		return $this->__get('position');
	}
	
	/**
	 * Get style
	 * @return string
	 */
	public function get_event(){
		return $this->__get('event');
	}
}