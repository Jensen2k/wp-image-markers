<?php

namespace WPIM\Field;

class IconPickerControl extends Control {

	public function __construct( $args = array() ) {

		$this->type = 'icon_picker';

		parent::__construct( $args );
	}

	public function render() {
		ob_start();
		?>
		<div class="wpim-field wpim-icon_picker">
			<?php
			if ( function_exists( 'wp_simple_iconfonts_field' ) ) {
				wp_simple_iconfonts_field( array(
					'id' => $this->name,
					'name' => $this->name,
					'value' => $this->value
				) );
			} else {
				echo '<div class="wpim-icon_picker__invalid">';
				printf( wp_kses( __( '%s Plugin should be activated to enable Icon Picker.', 'wp-image-markers' ), array( 'a' => array( 'href' => 1, 'target' => 1 ) ) ), '<a href="//wordpress.org/plugins/wp-simple-iconfonts/" target="_blank">WP Simple Iconfonts</a>' );
				echo '</div>';
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

}
