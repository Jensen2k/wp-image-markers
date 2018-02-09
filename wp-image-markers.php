<?php

/**
 * Plugin Name:     WP Image Markers - Easy Hotspot Solution
 * Plugin URI:      https://wordpress.org/plugins/wp-image-makers-easy-hotspot-solution/
 * Description:     Add and drag marker icons with tooltips in a image.    
 * Version:         1.0.0   
 * Author:          WeDesignWeBuild
 * Author URI:      https://profiles.wordpress.org/wedesignwebuild
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * Requires at least: 4.5
 * Tested up to: 4.9
 * Text Domain: wp-image-markers
 * Domain Path: /languages/
 *
 * @package    WPIM
 */
define( 'WPIM_VERSION', '1.0.0' );
define( 'WPIM_FILE', __FILE__ );
define( 'WPIM_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPIM_URL', plugin_dir_url( __FILE__ ) );
define( 'WPIM_TEMPLATE_PATH', WPIM_DIR . 'templates' );

/**
 * First, we need autoload via Composer to make everything works.
 */
require WPIM_DIR . 'vendor/autoload.php';

/**
 * Next, load the bootstrap file.
 */
require WPIM_DIR . 'bootstrap.php';

$GLOBALS['wpim'] = new WPIM;

function wpim( $make = null ) {

	if ( is_null( $make ) ) {
		return WPIM::get_instance();
	}

	return WPIM::get_instance()->factory( $make );
}
