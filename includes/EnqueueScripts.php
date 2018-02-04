<?php

namespace WPIM;

class EnqueueScripts {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
	}

	/**
	 * Register/queue frontend scripts.
	 * @return void
	 */
	public static function load_scripts() {

		wp_enqueue_style( 'wpim-style', WPIM_URL . '/assets/css/front.css', null, WPIM_VERSION );
		wp_enqueue_style( 'animate', WPIM_URL . '/assets/css/animate.css', null, '3.5.2' );
		wp_enqueue_script( 'wpim', WPIM_URL . '/assets/js/front.js', array( 'jquery' ), WPIM_VERSION );
		$arrCss = get_option( 'wpim_css', array() );

		if ( !empty( $arrCss ) && is_array( $arrCss ) ) {

			$css = implode( '', $arrCss );

			wp_add_inline_style( 'wpim-style', $css );
		}
	}

	public static function admin_scripts( $hook ) {

		if ( $hook == 'post.php' || $hook == 'post-new.php' ) {

			global $post;

			if ( $post->post_type == 'wp_image_markers' ) {

				$min = WP_DEBUG ? '' : '.min';

				wp_enqueue_style( 'wpim-admin', WPIM_URL . '/assets/css/admin.css', null, WPIM_VERSION );
				wp_enqueue_style( 'wpim-admin', WPIM_URL . '/assets/css/admin.css', null, WPIM_VERSION );
				wp_enqueue_script( 'wpim-dependency', WPIM_URL . 'assets/vendors/dependency/dependency.js', array( 'jquery' ), WPIM_VERSION );

				//Dialog
				wp_enqueue_media();
				wp_enqueue_script( 'jquery-ui-draggable' );
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script( 'jquery-ui-slider' );
				

				//Field color
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				wp_enqueue_script( 'wp-color-picker-alpha', WPIM_URL . 'assets/vendors/wp-color-picker-alpha/wp-color-picker-alpha' . $min . '.js', array( 'wp-color-picker' ), WPIM_VERSION );

				//Field Autocomplete
				wp_enqueue_script( 'selectize', WPIM_URL . 'assets/vendors/selectize/selectize' . $min . '.js', array( 'jquery' ), '0.12.4' );
				wp_enqueue_style( 'selectize', WPIM_URL . 'assets/vendors/selectize/selectize' . $min . '.css', null, '0.12.4' );
				wp_enqueue_style( 'selectize-skin', WPIM_URL . 'assets/vendors/selectize/selectize.default' . $min . '.css', null, '0.12.4' );

				//Field textarea html
				wp_enqueue_editor();

				//Script fields
				wp_enqueue_script( 'wpim-field', WPIM_URL . '/assets/js/fields.js', array( 'jquery' ), WPIM_VERSION, true );
				wp_enqueue_script( 'wpim-admin', WPIM_URL . '/assets/js/admin.js', array( 'jquery' ), WPIM_VERSION, true );

				$upload_dir = wp_upload_dir();
				
				$localize = array(
					'upload_url' => $upload_dir['baseurl'],
					'error_image_url' => WPIM_URL . 'assets/images/select2-spinner.gif',
					'marker_availables' => self::marker_availables()
				);

				wp_localize_script( 'wpim-admin', 'wpim_var', apply_filters( 'wpim_var', $localize ) );
			}
		}
	}

	public static function marker_availables() {
		
		$arr = include WPIM_DIR . 'includes/marker-availables.php';

		return apply_filters( 'wpim\get_marker_availables', $arr );
	}

}
