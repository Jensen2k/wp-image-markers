<?php

namespace WPIM\Field;

class TextGroupControl extends Control {

	public function __construct( $args = array() ) {

		$this->type = 'text_group';

		parent::__construct( $args );
	}

	public function render() {

		ob_start();
		?>
		<div class="wpim-field wpim-text_group">

			<input type="hidden" value="<?php echo esc_attr( json_encode( $this->value ) ) ?>" <?php echo implode( ' ', $this->input_attrs() ) ?>/>

			<?php
			foreach ( $this->options as $key => $args ) {

				if ( is_string( $args ) ) {
					$args = array( 'label' => $args, 'type' => 'textfield' );
				}

				$args = wp_parse_args( $args, array(
					'label' => '',
					'type' => 'textfield',
					'key' => '',
					'value' => '',
					'options' => array(),
					'alpha' => true,
					'id' => sprintf( '%s__%s', $this->name, $key )
						) );

				$args['value'] = isset( $this->value[$key] ) ? $this->value[$key] : '';
				$args['key'] = $key;

				echo '<div class="label">';
				echo '<label for="' . esc_attr( $args['id'] ) . '">' . esc_html( $args['label'] ) . '</label>';

				if ( method_exists( $this, $args['type'] ) ) {
					$this->{$args['type']}( $args );
				}

				echo '</div>';
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Display subfield: Textfield
	 * @param array $args
	 * 
	 * @return void
	 */
	public function textfield( $args = array() ) {
		printf( '<input id="%s" type="text" data-name="%s" value="%s"/>', $args['id'], $args['key'], $args['value'] );
	}

	/**
	 * Display subfield: Color
	 * @param array $args
	 * 
	 * @return void
	 */
	public function color( $args = array() ) {
		printf( '<input id="%4$s" type="text" data-name="%1$s" value="%2$s" data-default-color="%2$s" data-alpha="%3$s" />', $args['key'], $args['value'], $args['alpha'], $args['id'] );
	}

	/**
	 * Display subfield: Select
	 * @param array $args
	 * 
	 * @return void
	 */
	public function select( $args = array() ) {

		printf( '<span class="select-wrap"><select id="%s" data-name="%s">', $args['id'], $args['key'] );

		foreach ( $args['options'] as $key => $value ) {

			$selected = $key == $args['value'] ? 'selected' : '';

			printf( '<option %s value="%s">%s</option>', $selected, $key, $value );
		}

		echo '</select></span>';
	}

}
