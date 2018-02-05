<?php

namespace WPIM;
use WPIM\Admin;

class WPIM {

	/**
	 * The current globally available container (if any).
	 *
	 * @var static
	 */
	public static $instance;

	/**
	 * Set the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-image-markers' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-image-markers' ), '1.0.0' );
	}

	public $mailer;

	public function __construct() {

		static::$instance = $this;

		Installer::init();
		AjaxHandles::init();
		EnqueueScripts::init();
		new Admin\PostType();
		new Shortcodes();

		add_action( 'after_setup_theme', array( $this, 'load_plugin_textdomain' ) );
		
		do_action( 'wpim\loaded' );
	}
	
	/**
	 * Load Localisation files.
	 * @return void
	 */
	public function load_plugin_textdomain() {

		/**
		 * Set filter for plugin's languages directory
		 */
		$wpim_dir = WPIM_DIR . 'languages/';
		$wpim_dir = apply_filters( 'wpim\languages_directory', $wpim_dir );

		/**
		 * Traditional WordPress plugin locale filter
		 */
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-image-markers' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'wp-image-markers', $locale );

		$mofile_local = $wpim_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/wp-image-markers/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			load_textdomain( 'wp-image-markers', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			load_textdomain( 'wp-image-markers', $mofile_local );
		} else {
			load_plugin_textdomain( 'wp-image-markers', false, $wpim_dir );
		}
	}

	/**
	 * Resolve a object/property and call it if needed.
	 *
	 * @param  string $id Param ID.
	 * @return mixed
	 */
	public function factory( $id ) {

		if ( isset( $this->$id ) ) {
			return $this->$id;
		}
	}

}
