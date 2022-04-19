<?php
/*
* Plugin Name: Dead Link Checker
* Description: This plugin checks and remove YouTube deadlinks from posts contents.
* Author: Muhammad Kashif
* Text Domain: deadlinks
* Version: 1.1.0
*/

// Make sure we don't expose any info if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WH_PLUGIN_DIR_URL', __FILE__ );

require_once( WH_PLUGIN_DIR . 'includes/class.checkdeadlinks.php' );