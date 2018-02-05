<?php

namespace WPIM\Admin;

class Dialog {

	public function __construct() {
		
	}

	public function sanitize_values( $data ) {

		$marker = new \WPIM\Marker\Marker( $data );

		$data[$marker->get_type()] = $marker->get_value();

		if ( $marker->get_type() == 'icon' ) {
			$value = explode( ' ', $marker->get_value() );
			$data['icon'] = array(
				'type' => isset( $value[0] ) ? $value[0] : '',
				'icon' => isset( $value[1] ) ? $value[1] : '',
			);
		}
		
		$data['color__background'] = $marker->css->background( 'color' );
		$data['color__text'] = $marker->css->font( 'color' );

		$data['color__background_hover'] = $marker->css->background( 'color_hover' );
		$data['color__border_hover'] = $marker->css->border( 'color_hover' );
		$data['color__text_hover'] = $marker->css->font( 'color_hover' );


		$data['border_radius'] = $marker->css->radius();
		$data['box_shadow'] = $marker->css->shadow();
		$data['position'] = $marker->css->position();
		$data['size'] = $marker->css->size();
		$data['rotate'] = $marker->css->rotate();
		$data['font'] = $marker->css->font();

		$data['datasource'] = $marker->infowindow->get_datasource();

		if ( $data['datasource'] == 'paragraph' ) {
			$data['paragraph'] = $marker->infowindow->get_content();
		} else {
			$data['post_type'] = $marker->infowindow->get_content();
		}

		$data['window_position'] = $marker->infowindow->get_position();
		$data['window_event'] = $marker->infowindow->get_event();

		return $data;
	}

	public function form( $data ) {

		$post_types = get_post_types( array( 'public' => true, 'publicly_queryable' => true, 'exclude_from_search' => false, 'show_ui' => true ) );

		$post_types = array_map( function($key) {
			return ucfirst( $key );
		}, $post_types );

		unset( $post_types['attachment'] );
		$post_types = array( 'paragraph' => esc_attr__( 'Paragraph' ), 'page' => esc_attr__( 'Page', 'wp-image-markers' ) ) + $post_types;

		$accepts = $post_types;
		unset( $accepts['paragraph'] );

		$img = WPIM_URL . '/assets/images/markers/';

		$settings = new Settings( array(
			'id' => 'wp_image_markers',
			'title' => esc_html__( 'Settings', 'wp-image-markers' ),
			'values' => $this->sanitize_values( $data ),
			'fields' => array(
				array(
					'title' => esc_html__( 'Marker type', 'wp-image-markers' ),
					'name' => 'valueType',
					'type' => 'radio',
					'value' => '',
					'options' => array(
						'char' => esc_attr__( 'Text', 'wp-image-markers' ),
						'image' => esc_attr__( 'Image', 'wp-image-markers' ),
						'icon' => esc_attr__( 'Icon', 'wp-image-markers' )
					),
					'input_attrs' => array( 'inline' => 1 ),
					'group' => esc_html__( 'Marker', 'wp-image-markers' ),
				),
				array(
					'title' => esc_html__( 'Style', 'wp-image-markers' ),
					'name' => 'available',
					'type' => 'radio_image',
					'value' => '',
					'options' => array(
						'' => array(
							'url' => $img . 'marker-0.png',
							'title' => esc_attr__( 'Custom', 'wp-image-marker' ),
							'group' => array( 'char', 'image', 'icon' )
						),
						1 => array(
							'url' => $img . 'marker-1.jpg',
							'group' => array( 'icon' )
						),
						2 => array(
							'url' => $img . 'marker-2.jpg',
							'group' => array( 'icon' )
						),
						3 => array(
							'url' => $img . 'marker-3.jpg',
							'group' => array( 'icon' )
						),
						4 => array(
							'url' => $img . 'marker-4.jpg',
							'group' => array( 'icon' )
						),
						5 => array(
							'url' => $img . 'marker-5.jpg',
							'group' => array( 'icon' )
						),
						6 => array(
							'url' => $img . 'marker-6.jpg',
							'group' => array( 'char' )
						),
						7 => array(
							'url' => $img . 'marker-7.jpg',
							'group' => array( 'char' )
						),
						8 => array(
							'url' => $img . 'marker-8.jpg',
							'group' => array( 'char' )
						),
						9 => array(
							'url' => $img . 'marker-9.jpg',
							'group' => array( 'char' )
						),
						10 => array(
							'url' => $img . 'marker-10.jpg',
							'group' => array( 'image' )
						),
						11 => array(
							'url' => $img . 'marker-11.jpg',
							'group' => array( 'image' )
						),
					),
					'input_attrs' => array( 'inline' => 1, 'image_size' => array( '50px', '50px' ) ),
					'group' => esc_html__( 'Marker', 'wp-image-markers' ),
				),
				array(
					'title' => esc_html__( 'Text', 'wp-image-markers' ),
					'name' => 'char',
					'type' => 'text',
					'value' => '',
					'input_attrs' => array( 'class' => array( 'size-small' ) ),
					'dependency' => array(
						'valueType' => array( 'values' => array( 'char' ) ),
					),
					'descrition' => esc_html__( 'Enter a number or a letter in alphabet.', 'wp-image-markers' ),
					'group' => esc_html__( 'Marker', 'wp-image-markers' ),
				),
				array(
					'title' => esc_html__( 'Select Icon', 'wp-image-markers' ),
					'name' => 'icon',
					'type' => 'icon_picker',
					'value' => '',
					'dependency' => array(
						'valueType' => array( 'values' => array( 'icon' ) ),
					),
					'group' => esc_html__( 'Marker', 'wp-image-markers' ),
				),
				array(
					'title' => esc_html__( 'Select Image', 'wp-image-markers' ),
					'name' => 'image',
					'type' => 'image_picker',
					'value' => '',
					'group' => esc_html__( 'Marker', 'wp-image-markers' ),
					'dependency' => array(
						'valueType' => array( 'values' => array( 'image' ) ),
					),
				),
				array(
					'title' => esc_html__( 'Position', 'wp-image-markers' ),
					'name' => 'position',
					'type' => 'text_group',
					'value' => array( 'top' => 0, 'left' => 0 ),
					'options' => array(
						'top' => esc_attr__( 'Top', 'wp-image-markers' ),
						'left' => esc_attr__( 'Left', 'wp-image-markers' )
					),
					'group' => esc_html__( 'Marker', 'wp-image-markers' ),
					'description' => wp_kses( sprintf( __( 'Use px, %, em... check %s for more.', 'wp-image-markers' ), '<a target="_blank" href="http://www.w3schools.com/cssref/css_units.asp">Css units</a>' ), array( 'a' => array( 'href' => 1, 'target' => 1 ) ) )
				),
				//Styles group
				array(
					'title' => esc_html__( 'Background Color', 'wp-image-markers' ),
					'name' => 'color__background',
					'type' => 'color_picker',
					'value' => '',
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'dependency' => array(
						'valueType' => array( 'values' => array( 'char', 'icon' ) ),
					),
					'input_attrs' => array( 'data-alpha' => 'true' )
				),
				//Font
				array(
					'title' => esc_html__( 'Font', 'wp-image-markers' ),
					'name' => 'font',
					'type' => 'text_group',
					'value' => array( 'size' => '14px', 'weight' => 'bold', 'style' => 'normal', 'color' => '#ffffff' ),
					'options' => array(
						'size' => array(
							'label' => esc_html__( 'Size', 'wp-image-markers' ),
							'type' => 'textfield',
						),
						'weight' => array(
							'label' => esc_html__( 'Weight', 'wp-image-markers' ),
							'type' => 'select',
							'options' => array(
								'lighter' => esc_attr__( 'Lighter', 'wp-image-markers' ),
								'normal' => esc_attr__( 'Normal', 'wp-image-markers' ),
								'bold' => esc_attr__( 'Bold', 'wp-image-markers' ),
							)
						),
						'style' => array(
							'label' => esc_html__( 'Style', 'wp-image-markers' ),
							'type' => 'select',
							'options' => array(
								'normal' => esc_attr__( 'Normal', 'wp-image-markers' ),
								'italic' => esc_attr__( 'Italic', 'wp-image-markers' ),
							)
						),
						'color' => array(
							'label' => esc_html__( 'Color', 'wp-image-markers' ),
							'type' => 'color',
						)
					),
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'description' => wp_kses( sprintf( __( 'Allowed metrics: %s', 'wp-image-markers' ), '<a target="_blank" href="http://www.w3schools.com/cssref/css_units.asp">Css units</a>' ), array( 'a' => array( 'href' => 1, 'target' => 1 ) ) )
				),
				//Border
				array(
					'title' => esc_html__( 'Border', 'wp-image-markers' ),
					'name' => 'border',
					'type' => 'text_group',
					'value' => array( 'width' => '0px', 'style' => 'solid', 'color' => '' ),
					'options' => array(
						'width' => array(
							'label' => esc_html__( 'Width', 'wp-image-markers' ),
							'type' => 'textfield',
						),
						'style' => array(
							'label' => esc_html__( 'Style', 'wp-image-markers' ),
							'type' => 'select',
							'options' => array(
								'none' => esc_attr__( 'None', 'wp-image-markers' ),
								'dashed' => esc_attr__( 'Dashed', 'wp-image-markers' ),
								'dotted' => esc_attr__( 'Dotted', 'wp-image-markers' ),
								'double' => esc_attr__( 'Double', 'wp-image-markers' ),
								'solid' => esc_attr__( 'Solid', 'wp-image-markers' ),
								'zigzag' => esc_attr__( 'Zigzag', 'wp-image-markers' ),
								'solid-arrow-top' => esc_attr__( 'Solid arrow top', 'wp-image-markers' ),
								'solid-arrow-right' => esc_attr__( 'Solid arrow right', 'wp-image-markers' ),
								'solid-arrow-bottom' => esc_attr__( 'Solid arrow bottom', 'wp-image-markers' ),
								'solid-arrow-left' => esc_attr__( 'Solid arrow left', 'wp-image-markers' ),
							)
						),
						'color' => array(
							'label' => esc_html__( 'Color', 'wp-image-markers' ),
							'type' => 'color',
						)
					),
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'description' => wp_kses( sprintf( __( 'Allowed metrics: %s', 'wp-image-markers' ), '<a target="_blank" href="http://www.w3schools.com/cssref/css_units.asp">Css units</a>' ), array( 'a' => array( 'href' => 1, 'target' => 1 ) ) )
				),
				//Box Shadow
				array(
					'title' => esc_html__( 'Box shadow', 'wp-image-markers' ),
					'name' => 'box_shadow',
					'type' => 'text_group',
					'value' => array( 'h' => '0px', 'v' => '0px', 'blur' => '0px', 'color' => '' ),
					'options' => array(
						'h' => esc_attr__( 'Horizontal', 'wp-image-markers' ),
						'v' => esc_attr__( 'Vertical', 'wp-image-markers' ),
						'blur' => esc_attr__( 'Blur', 'wp-image-markers' ),
						'color' => array(
							'label' => esc_attr__( 'Color', 'wp-image-markers' ),
							'type' => 'color',
							'alpha' => true
						)
					),
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'description' => wp_kses( sprintf( __( 'Allowed metrics: %s', 'wp-image-markers' ), '<a target="_blank" href="http://www.w3schools.com/cssref/css_units.asp">Css units</a>' ), array( 'a' => array( 'href' => 1, 'target' => 1 ) ) )
				),
				//Radius
				array(
					'title' => esc_html__( 'Border radius', 'wp-image-markers' ),
					'name' => 'border_radius',
					'type' => 'slider',
					'value' => 100,
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'options' => array(
						'min' => 0,
						'max' => 100,
						'unit' => '%'
					),
				),
				array(
					'title' => esc_html__( 'Rotate', 'wp-image-markers' ),
					'name' => 'rotate',
					'type' => 'slider',
					'value' => 0,
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'options' => array(
						'min' => 0,
						'max' => 360,
						'unit' => 'deg'
					),
				),
				//Dimension
				array(
					'title' => esc_html__( 'Dimension', 'wp-image-markers' ),
					'name' => 'size',
					'type' => 'text_group',
					'value' => array( 'width' => 30, 'height' => 30, 'line-height' => 30 ),
					'options' => array(
						'width' => esc_attr__( 'Width', 'wp-image-markers' ),
						'height' => esc_attr__( 'Height', 'wp-image-markers' ),
						'line-height' => esc_attr__( 'Line height', 'wp-image-markers' )
					),
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'description' => wp_kses( sprintf( __( 'Allowed metrics: %s', 'wp-image-markers' ), '<a target="_blank" href="http://www.w3schools.com/cssref/css_units.asp">Css units</a>' ), array( 'a' => array( 'href' => 1, 'target' => 1 ) ) )
				),
				//Hover state
				array(
					'title' => esc_html__( 'Hover state', 'wp-image-markers' ),
					'name' => 'custom_hover',
					'type' => 'checkbox',
					'value' => 0,
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'description' => esc_html__( 'Use custom hover for this marker.', 'wp-image-markers' ),
				),
				array(
					'title' => esc_html__( 'Background Color', 'wp-image-markers' ),
					'name' => 'color__background_hover',
					'type' => 'color_picker',
					'value' => '',
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'dependency' => array(
						'valueType' => array( 'values' => array( 'char', 'icon' ) ),
						'custom_hover' => array( 'values' => array( 1 ) ),
					),
					'input_attrs' => array(
						'data-alpha' => 'true'
					)
				),
				array(
					'title' => esc_html__( 'Text Color', 'wp-image-markers' ),
					'name' => 'color__text_hover',
					'type' => 'color_picker',
					'value' => '',
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'dependency' => array(
						'valueType' => array( 'values' => array( 'char', 'icon' ) ),
						'custom_hover' => array( 'values' => array( 1 ) ),
					),
				),
				array(
					'title' => esc_html__( 'Border Color', 'wp-image-markers' ),
					'name' => 'color__border_hover',
					'type' => 'color_picker',
					'value' => '',
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'dependency' => array(
						'custom_hover' => array( 'values' => array( 1 ) ),
					),
					'input_attrs' => array(
						'data-alpha' => 'true'
					)
				),
				array(
					'title' => esc_html__( 'Select Image', 'wp-image-markers' ),
					'name' => 'image_hover',
					'type' => 'image_picker',
					'value' => '',
					'group' => esc_html__( 'Styles', 'wp-image-markers' ),
					'dependency' => array(
						'valueType' => array( 'values' => array( 'image' ) ),
						'custom_hover' => array( 'values' => array( 1 ) ),
					),
				),
				//Window Info
				array(
					'title' => esc_html__( 'Data source', 'wp-image-markers' ),
					'name' => 'datasource',
					'type' => 'select',
					'value' => '',
					'options' => $post_types,
					'group' => esc_html__( 'Info window', 'wp-image-markers' )
				),
				array(
					'name' => 'post_type',
					'type' => 'autocomplete',
					'title' => esc_html__( 'Select post', 'wp-image-markers' ),
					'value' => '',
					'desc' => esc_html__( 'Ajax select', 'wp-image-markers' ),
					'data' => array( 'post_type' => array( 'post' ) ),
					'min_length' => 3,
					'dependency' => array(
						'datasource' => array( 'values' => array_keys( $accepts ) ),
					),
					'group' => esc_html__( 'Info window', 'wp-image-markers' )
				),
				array(
					'title' => esc_html__( 'Paragraph', 'wp-image-markers' ),
					'name' => 'paragraph',
					'type' => 'textarea_html',
					'value' => esc_attr__( 'Hello world!', 'wp-image-markers' ),
					'group' => esc_html__( 'Info window', 'wp-image-markers' ),
					'dependency' => array(
						'datasource' => array( 'values' => array( 'paragraph' ) ),
					),
					'sanitize_callback' => 'stripcslashes'
				),
				array(
					'title' => esc_html__( 'Position', 'wp-image-markers' ),
					'name' => 'window_position',
					'type' => 'radio',
					'value' => 'top',
					'options' => array(
						'top' => esc_attr__( 'Top', 'wp-image-markers' ),
						'right' => esc_attr__( 'Right', 'wp-image-markers' ),
						'bottom' => esc_attr__( 'Bottom', 'wp-image-markers' ),
						'left' => esc_attr__( 'Left', 'wp-image-markers' )
					),
					'input_attrs' => array( 'inline' => 1 ),
					'group' => esc_html__( 'Info window', 'wp-image-markers' ),
					'description' => esc_html__( 'Info window will be appeared by position', 'wp-image-markers' )
				),
				array(
					'title' => esc_html__( 'Event', 'wp-image-markers' ),
					'name' => 'window_event',
					'type' => 'radio',
					'value' => 'hover',
					'options' => array(
						'hover' => esc_attr__( 'Hover', 'wp-image-markers' ),
						'click' => esc_attr__( 'Click', 'wp-image-markers' ),
					),
					'input_attrs' => array( 'inline' => 1 ),
					'group' => esc_html__( 'Info window', 'wp-image-markers' ),
					'description' => esc_html__( 'Info window will be appeared after the event is action.', 'wp-image-markers' )
				),
			)
				) );

		$settings->render();
	}

}
