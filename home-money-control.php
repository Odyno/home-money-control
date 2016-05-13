<?php
/**
 * Copyright 2012  Alessandro Staniscia  (email : alessandro@staniscia.net)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 *
 *
 *
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
define( '__HMC_VERSION__', "1.0.0" );
define( '__HMC_PATH__', plugin_dir_path( __HMC_FILE__ ) );
define( '__HMC_URL__', plugin_dir_url( __HMC_FILE__ ) );
define( '__PNAMESPANE__', 'home_money_control' );


/**
 * Dependecy with restapi plugin.
 */
require_once(plugin_dir_path( __HMC_FILE__ ) . '../rest-api/plugin.php');



// Load plugin class files.
require_once( 'includes/class-home-money-control.php' );

// Load plugin Utils.
require_once( 'includes/utils/class-hmc-time.php' );
require_once( 'includes/utils/class-hmc-utils.php' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_home_money_control() {
	$plugin = new Home_Money_Control( __FILE__, __HMC_VERSION__ );
	$plugin->run();

}

run_home_money_control();
