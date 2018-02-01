<?php

namespace WPIM\Marker;

class Image{
	
	protected $data = array(
		'src'=>'',
		'width'=>'',
		'height'=>'',
		'id'=>0
	);
	
	public function __construct($args=array()) {
		$this->data = wp_parse_args($args, $this->data);
	}
	
	/**
	 * Magic method get
	 * @return mixed
	 */
	public function __get( $name ) {
		return isset( $this->data[ $name ] ) ? $this->data[ $name ] : '';
	}
	
	/**
	 * Get image url
	 * @return int
	 */
	public function get_src(){
		return $this->__get('src');
	}
	
	/**
	 * Get image width
	 * @return int
	 */
	public function get_width(){
		return $this->__get('width');
	}
	
	/**
	 * Get image height
	 * @return int
	 */
	public function get_height(){
		return $this->__get('height');
	}
	
	/**
	 * Get image id
	 * @return int
	 */
	public function get_id(){
		return $this->__get('id');
	}
	
}