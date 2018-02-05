<?php

namespace WPIM\Admin;

class Metabox {

	private $id;
	private $title;
	private $post_type;
	private $priority;
	private $context;
	private $fields;

	/**
	 * @access private
	 * @var string Container of the field in markup HTML
	 */
	private $field_wrapper;

	/**
	 * @access private
	 * @var array Group fields
	 */
	private $group_fields = array();

	public function __construct( $args ) {

		$defaults = array(
			'id' => 'wpim_metabox',
			'post_type' => 'wp_image_markers',
			'title' => esc_html__( 'WPIM Metabox', 'wp-image-markers' ),
			'context' => 'advanced',
			'priority' => 'low',
			'fields' => array(
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			if ( isset( $args[$key] ) ) {
				$this->$key = $args[$key];
			}
		}

		add_action( 'add_meta_boxes', array( $this, 'register' ) );
		add_action( "save_post_{$this->post_type}", array( $this, 'save' ), 1, 2 );
	}

	/**
	 * Register hook
	 * @return void
	 */
	public function register() {

		$this->field_wrapper = '<div class="wpim_form_row" %3$s><div class="col-label">%1$s</div><div class="col-field">%2$s</div></div>';

		add_meta_box( $this->id, $this->title, array( $this, 'render' ), $this->post_type, $this->context, $this->priority, $this->fields );
	}

	/**
	 * Process field output
	 * 
	 * @global Object $post
	 * @param array $args
	 * @return string Html
	 */
	public function render( $post, $args ) {

		$output = '';

		$output .= sprintf( '<div class="wpim-metabox wpim-metabox_%s">', $this->id );

		$output .= sprintf( '<input type="hidden" name="%s_nonce" value="%s" />', $this->id, wp_create_nonce( $this->id ) );

		foreach ( $this->fields as $field ) {
			/**
			 * Field value
			 */
			$value = get_post_meta( $post->ID, $field['name'], false );

			if ( isset( $value[0] ) ) {
				$value = $value[0];
			} elseif ( empty( $value[0] ) ) {
				$value = isset( $field['value'] ) ? $field['value'] : '';
			} else {
				$value = '';
			}

			$field['value'] = $value;

			/**
			 * Before render field type
			 */
			do_action( 'WPIM\Field\before_render_' . $field['type'], $field );

			/**
			 * Render field
			 */
			$field_output = $this->field_render( $field );

			/**
			 * Add field to group
			 */
			if ( $field_output != '' ) {
				$group = !empty( $field['group'] ) ? $field['group'] : '';
				if ( empty( $this->group_fields[$group] ) ) {
					$this->group_fields[$group] = array();
				}
				$this->group_fields[$group][] = $field_output;
			}
		}

		if ( count( $this->group_fields ) == 1 && !key( $this->group_fields ) ) {
			$output .= implode( '', $this->group_fields[''] );
		} else {
			$nav = '';
			$content = '';
			$index = 0;
			foreach ( $this->group_fields as $name => $fields ) {

				$name = empty( $name ) ? esc_html__( 'General', 'wp-image-markers' ) : $name;
				$index++;
				$active = $index == 1 ? 'active' : '';
				$id = $this->id . '-group_' . $index;
				$nav .= sprintf( '<li><a href="#%s" class="%s">%s</a></li>', $id, $active, $name );
				$content .= sprintf( '<div id="%s" class="group_item %s">%s</div>', $id, $active, implode( '', $fields ) );
			}

			$output .= '<div class="wpim_group">';
			$output .= '<ul class="group_nav">' . $nav . '</ul>';
			$output .= '<div class="group_panel">' . $content . '</div>';
			$output .= '</div>';
		}

		$output .= '</div>';

		print $output;
	}

	/**
	 * Process field
	 * @access private
	 * @return string Field Html
	 */
	private function field_render( $field ) {

		$control = new Field( $field );

		$required = $control->required ? '<span>*</span>' : '';

		$lable = !empty( $control->title ) ? sprintf( '<label for="%1$s">%2$s %3$s</label>', $control->name, $control->title, $required ) : '';

		$desc = !empty( $control->description ) ? sprintf( '<p class="description">%s</p>', $control->description ) : '';

		$attrs = sprintf( 'data-param_name="%s" ', $control->name );

		$attrs .= !empty( $field['dependency'] ) && is_array( $field['dependency'] ) ? 'data-dependency="' . esc_attr( json_encode( $field['dependency'] ) ) . '"' : '';

		if ( isset( $control->sanitize_callback ) && function_exists( $control->sanitize_callback ) ) {
			$control->value = call_user_func( $control->sanitize_callback, $control->value );
		}

		$field_output = $control->render() . $desc;

		$field_output = sprintf( $this->field_wrapper, $lable, $field_output, $attrs );

		return $field_output;
	}

	/**
	 * Save post meta
	 * @param int $post_id
	 * @param object|WP_Post $post
	 */
	public function save( $post_id, $post ) {

		/* don't save if $_POST is empty */
		if ( empty( $_POST ) )
			return $post_id;

		/* don't save during autosave */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		/* verify nonce */
		if ( !isset( $_POST[$this->id . '_nonce'] ) || !wp_verify_nonce( $_POST[$this->id . '_nonce'], $this->id ) )
			return $post_id;

		/* check permissions */
		if ( isset( $_POST['post_type'] ) && 'page' == sanitize_text_field( $_POST['post_type'] ) ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		foreach ( $this->fields as $field ) {

			if ( !isset( $field['name'] ) ) {
				continue;
			}

			$control = new Field( $field );

			$value = '';

			if ( isset( $_POST[$control->name] ) || $control->type == 'checkbox' ) {

				$input_value = isset( $_POST[$control->name] ) ? $_POST[$control->name] : '';

				if ( function_exists( $control->sanitize_callback ) ) {
					$value = call_user_func( $control->sanitize_callback, $input_value );
				} else {

					if ( $control->multiple ) {
						$value = maybe_unserialize( $input_value );
					} elseif ( $control->type == 'checkbox' ) {
						$value = !empty( $input_value ) ? 1 : 0;
					} elseif ( $control->type == 'link' ) {
						$value = strip_tags( $input_value );
					} elseif ( $control->type == 'textarea' ) {

						$value = wp_kses( trim( wp_unslash( $input_value ) ), wp_kses_allowed_html( 'post' ) );
					} else {
						$value = sanitize_text_field( $input_value );
					}
				}

				/**
				 * Allow third party filter value
				 */
				$value = apply_filters( "WPIM\Field\metabox_{$control->type}", $value, $input_value );

				update_post_meta( $post_id, $control->name, $value );
			} else {
				delete_post_meta( $post_id, $control->name );
			}
		}
	}

}
