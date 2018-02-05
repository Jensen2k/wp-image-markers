<?php

namespace WPIM\Field;

class TextareaHtmlControl extends Control {

	public function __construct( $args = array() ) {

		$this->type = 'textarea_html';

		parent::__construct( $args );
	}

	public function render() {

		if ( !function_exists( 'wp_editor' ) ) {
			return $this->field_textarea();
		}

		ob_start();
		
		?>
		<div class="wpim-field wpim-textarea_html">
			<?php
			if ( user_can_richedit() ) {
				add_filter( 'the_editor_content', 'format_for_editor', 10, 2 );
				$default_editor = 'tinymce';
			} else {
				$default_editor = 'html';
			}

			/** This filter is documented in wp-includes/class-wp-editor.php */
			$text = apply_filters( 'the_editor_content', $this->value, $default_editor );

			// Reset filter addition.
			if ( user_can_richedit() ) {
				remove_filter( 'the_editor_content', 'format_for_editor' );
			}

			// Prevent premature closing of textarea in case format_for_editor() didn't apply or the_editor_content filter did a wrong thing.
			$escaped_text = preg_replace( '#</textarea#i', '&lt;/textarea', $text );
			?>

			<textarea id="<?php echo esc_attr( $this->name ) ?>" name="<?php echo esc_attr( $this->name ) ?>" class="widefat wpim_value"><?php echo stripcslashes( $escaped_text); ?></textarea>
		</div>
		<?php
		return ob_get_clean();
	}

}
