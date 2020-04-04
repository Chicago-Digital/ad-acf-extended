<?php

/*
Plugin Name: Advanced Custom Fields: Antenna Digital Extended
Plugin URI: https://www.antennagroup.com
Version: 1.0.8
Author: Antenna | Digital
Author URI: https://www.antennagroup.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('AD_ACF_extended') ) :

include_once( plugin_dir_path( __FILE__ ) . 'updater.php' );
$updater = new AD_ACF_Updater( __FILE__ );
$updater->set_username( 'Chicago-Digital' );
$updater->set_repository( 'ad-acf-extended' );
$updater->initialize();

class AD_ACF_extended {

	// vars
	var $settings;


	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @param	void
	*  @return	void
	*/

	function __construct() {

		// settings
		// - these will be passed into the field class.
		$this->settings = array(
			'version'	=> '1.0.8',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);

		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field')); // v5

	}

	/*
	*  include_field
	*
	*  This function will include the field type class
	*
	*  @param	$version (int) major ACF version. Defaults to false
	*  @return	void
	*/

	function include_field( $version = false ) {

		// load textdomain
		load_plugin_textdomain( 'ad-acf-extended', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );

		// include
		include_once('fields/class-AD_ACF-acf-field-NAV-MENU.php');
		// include
		include_once('fields/class-AD_ACF-acf-field-LINK-EXTENDED.php');
		// include
		include_once('fields/class-AD_ACF-acf-field-ICON-PICKER.php');
	}

}


// initialize
new AD_ACF_extended();


// class_exists check
endif;

?>
