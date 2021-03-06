<?php

namespace WPIM\Admin;
use WPIM\Field\ControlVars;

class Field {
	
	use ControlVars;

	private $control;

	public function __construct( $args = array() ) {

		$class = $this->control_class( $args['type'] );
	
		foreach ( $args as $name => $value ) {
			$this->__set( $name, $value );
		}
		

		$this->control = new $class( $args );
		
	}

	public function __set( $name, $value ) {
		$this->$name = $value;
	}

	public function render() {
		
		return $this->control->render();
	}

	private function control_class( $type ) {

		$types = explode( '_', $type );

		$types = array_map( function($value) {
			return ucfirst( $value );
		}, $types );

		$class = implode( '', $types );
		$class = "WPIM\\Field\\{$class}Control";
		
		if ( class_exists( $class ) ) {
			return $class;
		}

		return "WPIM\\Field\\Control";
	}

}
