<?php

namespace WPIM\Field;

class SliderControl extends Control {

	public function __construct( $args = array() ) {

		$this->type = 'slider_html';

		parent::__construct( $args );

		$this->options = wp_parse_args( $this->options, array(
			'label' => '',
			'min' => 0,
			'max' => 10,
			'range' => 'min',
			'unit' => 'px',
			'width' => '60%',
			'step' => 1
				) );
		$this->options['value'] = $this->value;
	}

	public function render() {

		ob_start();
		?>
		<div style="width: <?php echo esc_attr( $this->options['width'] ) ?>" class="wpim-field wpim-slider" data-options="<?php echo esc_attr( json_encode( $this->options ) ) ?>">
			<span class="wpim-slider__label"><?php echo esc_html( $this->options['label'] . $this->value . $this->options['unit'] ) ?></span>
			<div class="wpim-slider__bar"></div>
			<?php printf( '<input type="hidden" value="%s" %s/>', esc_attr( $this->value ), implode( ' ', $this->input_attrs() ) ); ?>
		</div>
		<?php
		return ob_get_clean();
	}

}
