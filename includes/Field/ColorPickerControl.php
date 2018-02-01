<?php

namespace WPIM\Field;

class ColorPickerControl extends Control {

	public function __construct( $args = array() ) {

		$this->type = 'color_picker';

		parent::__construct( $args );
	}

	public function render() {
		return sprintf( '<input type="text" value="%1$s" data-default-color="" %2$s/>', htmlspecialchars( $this->value ), implode( ' ', $this->input_attrs() ) );
	}

}
