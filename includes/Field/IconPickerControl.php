<?php

namespace WPIM\Field;

class IconPickerControl extends Control {

	/**
	 * @var string Front Icon source
	 */
	public $font;

	public function __construct( $args = array() ) {

		$this->type = 'icon_picker';

		$this->font = isset( $args['font'] ) ? $args['font'] : 'fontawesome';

		parent::__construct( $args );
	}

	public function render() {

		if ( !has_filter( 'wpim\source_' . $this->font ) ) {
			$this->font = 'fontawesome';
		}

		$arr = apply_filters( 'wpim\source_' . $this->font, array() );

		ob_start();
		?>
		<div class="wpim-field wpim-icon_picker">
			<select class="wpim_value" <?php echo implode( ' ', $this->input_attrs() ) ?>>
				<?php
				if ( !empty( $arr ) ) {
					echo '<option value="">' . esc_attr__( 'No icon', 'wp-image-markers' ) . '</option>';
					foreach ( $arr as $group => $icons ) {

						if ( !is_array( $icons ) || !is_array( current( $icons ) ) ) {
							$class_key = key( $icons );
							echo '<option value="' . esc_attr( $class_key ) . '" ' . ( strcmp( $class_key, $this->value ) === 0 ? 'selected' : '' ) . '>' . esc_html( current( $icons ) ) . '</option>' . "\n";
						} else {

							echo '<optgroup label="' . esc_attr( $group ) . '">' . "\n";
							foreach ( $icons as $key => $label ) {
								$class_key = key( $label );
								echo'<option value="' . esc_attr( $class_key ) . '" ' . ( strcmp( $class_key, $this->value ) === 0 ? 'selected' : '' ) . '>' . esc_html( current( $label ) ) . '</option>' . "\n";
							}
							echo'</optgroup>' . "\n";
						}
					}
				}
				?>
			</select>
		</div>
		<?php
		return ob_get_clean();
	}
}
