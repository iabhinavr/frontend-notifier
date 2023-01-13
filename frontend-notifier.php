<?php

/**
 * The plugin bootstrap file
 * 
 * This file is read by WordPress to generate the plugin information in the 
 * plugin admin area. This file also defines a function that starts the plugin.
 * 
 * @link    https://codingreflections.com
 * @since   1.0.0
 * @package Frontend_Notifier
 * 
 * @wordpress-plugin
 * Plugin Name: Frontend Notifier
 * Plugin URI:  https://codingreflections.com
 * Description: Plugin to keep track of new and updated posts and notify the frontend for headless wp sites.
 * Version:     1.0.0
 * Author:      Abhinav R
 * Author URI:  https://abhinavr.me
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gps-2.0.txt
 */

// If this file is called directly, abort.

if( !defined( 'WPINC' ) ) {
    die;
}

if(!defined( 'FN_SECRET_KEY' ) || !defined( 'FN_URL' )) {
    die;
}

define( 'FN_PLUGIN_DIR', plugin_dir_path(__FILE__) );

foreach( glob( FN_PLUGIN_DIR . 'admin/*.php' ) as $file ) {
    include_once $file;
}

add_action( 'plugins_loaded', 'frontend_notifier' );

/**
 * Starts the plugin.
 * 
 * @since 1.0.0
 */

function frontend_notifier() {

    $frontend_notifier = new Frontend_Notifier();

    $frontend_notifier->init();

}




