<?php

namespace WPIM\Field;

class ImagePickerControl extends Control {

	public function __construct( $args = array() ) {

		$this->type = 'image_picker';

		parent::__construct( $args );
	}

	public function render() {
		ob_start();

		$uniqid = uniqid();
		?>
		<div class="wpim-field wpim-image_picker" data-multiple="<?php echo esc_attr( $this->multiple ) ?>" id="wpim-image-<?php echo esc_attr( $uniqid ) ?>">
			<?php
			printf( '<input type="hidden" class="attach_images wpim_value" value="%s" %s/>', $this->value, implode( ' ', $this->input_attrs() ) );

			$value = explode( ',', trim( $this->value ) );
			?>
			<div class="attached_images">

				<ul class="image_list">
					<?php
					if ( !empty( $value[0] ) && sizeof( $value ) > 0 ) {
						foreach ( $value as $str ) {
							$arr = explode( '|', $str );
							if ( !empty( $arr[0] ) && sizeof( $arr ) > 0 ) {
								$id = $arr[0];
								?>
								<li class="added" data-id="<?php echo esc_attr( $id ) ?>">
									<div class="inner">
										<?php echo wp_get_attachment_image( $id, 'thumbnail' ) ?>
									</div>
									<a href="#" class="remove" title="<?php echo esc_attr__( 'Remove', 'wp-image-markers' ) ?>"></a>
								</li>
								<?php
							}
						}
					}
					?>

				</ul>

				<a class="add_images" href="#" title="<?php echo esc_attr__( 'Add images', 'wp-image-markers' ) ?>"><?php echo esc_attr__( 'Add images', 'wp-image-markers' ) ?></a>

			</div>
		</div>
		<?php
		return ob_get_clean();
	}

}
