<?php
/*
Plugin Name: Custom Product List Table
Plugin URI: https://viitorcloud.com/blog/
Description: This plugin handles Product's Add,Edit and Delete functinalities with   Category and product's status. 
Version: 1.0.0
Author: Mitali, Viitorcloud
Author URI: https://viitorcloud.com/
*/

/**
 * Define Some needed predefined variables
 * 
 * @package   Category List Table
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if( !defined( 'WW_CLT_DIR' ) ) {
	define( 'WW_CLT_DIR', dirname( __FILE__ ) ); // plugin dir
}
if(!defined('WW_CLT_TEXT_DOMAIN')) { //check if variable is not defined previous then define it
	define('WW_CLT_TEXT_DOMAIN','wwclt'); //this is for multi language support in plugin
}
if( !defined( 'WW_CLT_ADMIN' ) ) {
	define( 'WW_CLT_ADMIN', WW_CLT_DIR . '/includes/admin' ); // plugin admin dir
}
if(!defined('wwcltlevel')) { //check if variable is not defined previous then define its
	define('wwcltlevel','manage_options'); //this is capability in plugin
}
if(!defined('WW_CLT_URL')) {
	define('WW_CLT_URL',plugin_dir_url( __FILE__ ) ); // plugin url
}
if( !defined( 'WW_CLT_TAXONOMY' )) {
	define( 'WW_CLT_TAXONOMY','ww_pro_category' );
}
if( !defined( 'WW_CLT_POST_TYPE' )) {
	define( 'WW_CLT_POST_TYPE','ww_pro_post' );
}
//metabox prefix
if( !defined( 'WW_CLT_META_PREFIX' )) {
	define( 'WW_CLT_META_PREFIX', '_ww_clt_' );
}

/**
 * Load Text Domain
 * 
 * This gets the plugin ready for translation.
 * 
 * @package  Category List Table
 * @since 1.0.0
 */
load_plugin_textdomain( 'wwclt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Plugin Activation hook
 * 
 * This hook will call when plugin will activate
 * 
 * @package  Category List Table
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'ww_clt_install' );

function ww_clt_install() {
	
	global $wpdb;
	
	//adding custom post type function
	ww_clt_reg_create_post_type();
	
	//IMP Call of Function
	//Need to call when custom post type is being used in plugin
	flush_rewrite_rules();
	
}


/**
 * Plugin Deactivation hook
 * 
 * This hook will call when plugin will deactivate
 * 
 * @package  Category List Table
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, 'ww_clt_uninstall' );

function ww_clt_uninstall() {
	
	global $wpdb;
	
	//IMP Call of Function
	//Need to call when custom post type is being used in plugin
	flush_rewrite_rules();
}

/**
 * Includes Class Files
 * 
 * @package  Category List Table
 * @since 1.0.0
 */
global $ww_clt_model,$ww_clt_scripts,$ww_clt_admin;


//includes post types file
include_once( WW_CLT_DIR . '/includes/ww-clt-post-types.php');

//includes model class
require_once( WW_CLT_DIR . '/includes/class-ww-clt-model.php');
$ww_clt_model = new Ww_Clt_Model();

//includes scripts class file
require_once ( WW_CLT_DIR .'/includes/class-ww-clt-scripts.php');
$ww_clt_scripts = new Ww_Clt_Scripts();
$ww_clt_scripts->add_hooks();

//includes admin pages
require_once( WW_CLT_ADMIN . '/class-ww-clt-admin.php');
$ww_clt_admin = new Ww_Clt_Admin_Pages();
$ww_clt_admin->add_hooks();
?>