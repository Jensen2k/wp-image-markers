<?php

namespace WPIM;

class AjaxHandles {

	/**
	 * Hook in ajax handlers.
	 * 
	 * @return void
	 */
	public static function init() {

		self::add_ajax_events( array(
			'dialog_open' => false
		) );
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 * 
	 * @param array $ajax_events
	 * @return void
	 */
	public static function add_ajax_events( $ajax_events ) {

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_wpim_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_wpim_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	public static function dialog_open() {

		if ( isset( $_POST['nonce'] ) && !wp_verify_nonce( $_POST['nonce'], 'wpim_markers' ) ) {
			echo esc_html__( 'Security key is invalid.', 'wp-image-imarkers' );
		}

		if ( isset( $_POST['data'] ) ) {
			$dialog = new Admin\Dialog();
			$dialog->form(  $_POST['data'] );
		}
		
		die();
	}

}
