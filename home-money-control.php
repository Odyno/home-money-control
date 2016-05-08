<?php
/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.staniscia.net
 * @package           WordPress
 * @author            Alessandro Staniscia
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Home Money Control
 * Plugin URI:        http://www.staniscia.net
 * Description:       This plugin add Rest interface for the managemen and report of your bill and profict.
 * Version:           1.0.0
 * Author:            Alessandro Staniscia
 * Author URI:        http://www.staniscia.net
 * Requires at least: 4.0
 * Tested up to:      4.0
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       home-money-control
 * Domain Path:       /lang
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * The core plugin definition and class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */


define( '__HMC_FILE__', __FILE__ );
define( '__HMC_PATH__', plugin_dir_path( __HMC_FILE__ ) );
define( '__HMC_URL__', plugin_dir_url( __HMC_FILE__ ) );
define( '__PNAMESPANE__', 'home_money_control' );


// Load plugin class files.
require_once( 'includes/class-home-money-control.php' );
require_once( 'includes/class-home-money-control-settings.php' );









// Load plugin libraries.
require_once( 'includes/lib/class-home-money-control-admin-api.php' );
require_once( 'includes/lib/class-home-money-control-post-type.php' );
require_once( 'includes/lib/class-home-money-control-taxonomy.php' );

// Load plugin Utils.
require_once( 'includes/utils/class-hmc-time.php' );
require_once( 'includes/utils/class-hmc-utils.php' );

// Load Category Manager.
require_once( 'includes/category/class-hmc-voice-type.php' );
require_once( 'includes/category/class-hmc-voice.php' );
require_once( 'includes/category/class-hmc-category.php' );
require_once( 'includes/transactions/class-hmc-field.php' );
require_once( 'includes/transactions/class-hmc-transactions.php' );


// LOAD API.
/**
 * Init of function.
 */
function hmc_api_init() {
	require_once dirname( __FILE__ ) . '/includes/api/class-hmc-api-category.php';
	require_once dirname( __FILE__ ) . '/includes/api/class-hmc-api-transaction.php';
	add_filter( 'json_endpoints', array( new HMC_API_Category(), 'register_routes' ) );
	add_filter( 'json_endpoints', array( new Hmc_API_Transaction(), 'register_routes' ) );
}

add_action( 'wp_json_server_before_serve', 'hmc_api_init' );


/**
 * Returns the main instance of _home_money_control to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object _home_money_control
 */
function _home_money_control() {
	$instance = Home_Money_Control::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Home_Money_Control_Settings::instance( $instance );
	}

	return $instance;
}

_home_money_control();
