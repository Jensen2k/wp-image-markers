<?php

namespace WPIM;

class Installer {

	public static function init() {
		register_activation_hook( WPIM_FILE, array( __CLASS__, 'install' ) );
		register_deactivation_hook( WPIM_FILE, array( __CLASS__, 'uninstall' ) );
	}
	
	
	public static function install(){
		
	}
	
	public static function uninstall(){
		
	}
}
