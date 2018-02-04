<?php
/**
 * Template part for displaying image and markers
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WPIM
 * @since 1.0
 * @version 1.0
 */
$markers = $collection->getMarkers();

$image = new \WPIM\Marker\Image( $collection->getImage() );
?>
<div class="wpim wpim-<?php echo esc_attr( $collection->getId() ) ?>">

	<div class="wpim-image">
		<?php echo wp_get_attachment_image( $image->get_id(), 'full' ) ?>
	</div>

	<?php
	foreach ( $markers as $marker ) {

		$marker = new \WPIM\Marker\Marker( $marker );

		$markerClass = array(
			'wpim-marker',
			'wpim-marker--' . $marker->infowindow->get_event(),
			'wpim-marker--' . $marker->get_type(),
			'wpim-' . $collection->getId() . '__' . $marker->get_id(),
		);

		if ( $marker->css->image_hover() ) {
			$markerClass[] = 'wpim-marker--image-hover';
		}

		$attrs = array(
			'data-id="' . $marker->get_id() . '"',
			'id="' . $marker->get_id() . '"',
			'class="' . implode( ' ', $markerClass ) . '"',
		);

		$border_style = 'wpim-marker__icon--' . $marker->css->border( 'style' );

		printf( '<div %s><div class="wpim-marker__icon %s">%s</div>', implode( ' ', $attrs ), $border_style, $marker->get_value_html() );

		if ( $marker->infowindow->get_content() ) {

			$infowindowClass = array(
				'wpim-infowindow',
				'wpim-infowindow--' . $marker->infowindow->get_position(),
				'wpim-infowindow--' . $marker->infowindow->get_datasource()
			);

			echo '<div class="' . implode( ' ', $infowindowClass ) . '"><a class="wpim-infowindow__close" href="#"><span class="screen-reader-text">'. esc_html__( 'Close','wp-image-markers').'</span></a><div class="wpim-infowindow__inner">';

			$content_path = 'infowindow/content-' . $marker->infowindow->get_datasource();

			if ( !file_exists( wpim_template_path( $content_path ) ) ) {
				$content_path = 'infowindow/content-post';
			}

			wpim_template( $content_path, array(
				'infowindow' => $marker->infowindow
			) );

			echo '</div></div>';
		}

		echo '</div>';
	}
	?>
</div>