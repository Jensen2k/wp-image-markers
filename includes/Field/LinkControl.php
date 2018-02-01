<?php

namespace WPIM\Field;

class LinkControl extends Control {

	public function __construct( $args = array() ) {

		$this->type = 'link';

		parent::__construct( $args );
	}

	public function render() {

		$link = wpim_build_link( $this->value );

		$json_value = htmlentities( json_encode( $link ), ENT_QUOTES, 'utf-8' );

		$this->value = htmlentities( $this->value, ENT_QUOTES, 'utf-8' );

		ob_start();
		?>
		<div class="wpim-field wpim-link" id="wpim-link-<?php echo esc_attr( uniqid() ) ?>">

			<?php printf( '<input type="hidden" data-json="%1$s" %2$s/>', $json_value, implode( ' ', $this->input_attrs() ) ); ?>

			<a href="#" class="button link_button"><?php echo esc_attr__( 'Select URL', 'wp-image-markers' ) ?></a> 
			<span class="group_title">
				<span class="link_label_title link_label"><?php echo esc_attr__( 'Link Text:', 'wp-image-markers' ) ?></span> 
				<span class="title-label"><?php echo isset( $link['title'] ) ? esc_attr( $link['title'] ) : ''; ?></span> 
			</span>
			<span class="group_url">
				<span class="link_label"><?php echo esc_attr__( 'URL:', 'wp-image-markers' ) ?></span> 
				<span class="url-label">
					<?php
					echo isset( $link['url'] ) ? esc_url( $link['url'] ) : '';
					echo isset( $link['target'] ) ? ' ' . esc_attr( $link['target'] ) : '';
					?> 
				</span>
			</span>
		</div>
		<?php
		return ob_get_clean();
	}

}
