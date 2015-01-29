<?php
/*
Plugin Name: My Dashboard
Plugin URL: 
Description: A nice plugin to create your custom dashboard page
Version: 0.1
Author: Vinod Kumar
Author URI: https://www.facebook.com/v9ddhundhara
Contributors: 
Text Domain: rc_scd
*/

/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/

// plugin folder url
if(!defined('RC_SCD_PLUGIN_URL')) {
	define('RC_SCD_PLUGIN_URL', plugin_dir_url( __FILE__ ));
}

/*
|--------------------------------------------------------------------------
| MAIN CLASS
|--------------------------------------------------------------------------
*/

class rc_sweet_custom_dashboard {
 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
 
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
	
		add_action('admin_menu', array( &$this,'rc_scd_register_menu') );
		add_action('load-index.php', array( &$this,'rc_scd_redirect_dashboard') );
 
	} // end constructor
 
	function rc_scd_redirect_dashboard() {
	
		if( is_admin() ) {
			$screen = get_current_screen();
			
			if( $screen->base == 'dashboard' ) {

				//wp_redirect( admin_url( 'index.php?page=vinod-dashboard' ) );
				
			}
		}
	}
	
	
	
	function rc_scd_register_menu() {
		add_dashboard_page( 'My Dashboard', 'My Dashboard', 'read', 'vinod-dashboard', array( &$this,'rc_scd_create_dashboard') );
	}
	
	function rc_scd_create_dashboard() {
		include_once( 'my_dashboard.php'  );
	}

 
}
 
// instantiate plugin's class
$GLOBALS['My_dashboard'] = new rc_sweet_custom_dashboard();
?>