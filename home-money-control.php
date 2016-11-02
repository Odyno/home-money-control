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
 * @wordpress-plugin
 * Plugin Name:       Home Money Control
 * Plugin URI:        http://www.staniscia.net
 * Description:       This plugin add to wordpress the Rest interface for the management and report of your bill and profict and simple page to managemnt/report of it
 * Version:           1.0.10
 * Author:            Alessandro Staniscia
 * Author URI:        http://www.staniscia.net
 * Requires at least: 4.0
 * Tested up to:      4.6
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       home-money-control
 * Domain Path:       /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


require 'plugin-update-checker/plugin-update-checker.php';
$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
$myUpdateChecker = new $className(
	'https://github.com/Odyno/home-money-control',
	__FILE__,
	'master'
);



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
require_once( __HMC_PATH__  . '../rest-api/plugin.php' );


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





