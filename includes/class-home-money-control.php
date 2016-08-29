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
 * This class define the main class of plugin
 *
 * @package WordPress
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Home_Money_Control
 */
class Home_Money_Control {

	/**
	 * The version number.
	 *
	 * @var _version string Version of plugin
	 */
	private $_version;

	/**
	 * The main plugin file.
	 *
	 * @var     file string
	 * @access  public
	 * @since   1.0.0
	 */
	private $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	private $dir;

	/**
	 * The dir path.
	 *
	 * @var string
	 */
	private $dir_path;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	private $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	private $assets_url;

	/**
	 * Suffix for Javascripts.
	 *
	 */
	private $script_suffix;


	/**
	 * Pluto.
	 *
	 * @var $database_handler  AA.
	 */
	private $database_handler;

	/**
	 * Pippo.
	 *
	 * @var $api_handler AA.
	 */
	private $api_handler;

	/**
	 * Home_Money_Control constructor.
	 *
	 * @param string $file The filename position.
	 * @param string $version The current version of plugin.
	 */
	public function __construct( $file, $version ) {

		$this->_version    = $version;
		$this->plugin_slug = 'HMC';

		// Load plugin environment variables.
		$this->file          = $file;
		$this->dir           = dirname( $this->file );
		$this->dir_path      = plugin_dir_path( $this->file );
		$this->assets_dir    = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url    = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		

		// Load dependencies.
		$this->load_dependencies();

		register_activation_hook( $this->file, array( $this, 'on_activation' ) );
		register_deactivation_hook( $this->file, array( $this, 'on_deactivation' ) );
		register_uninstall_hook( $this->file, array( 'Home_Money_Control', 'on_uninstall' ) );

		// Handle localisation.
		$this->load_plugin_textdomain();

	}
	public function get_version(){
		return $this->_version;
	}


	/**
	 *
	 * Load the dependencies of this plugin.
	 */
	private function load_dependencies() {

		// load Dependecy
		require_once( __HMC_PATH__ . 'includes/utils/class-hmc-time.php' );
		require_once( __HMC_PATH__ . 'includes/utils/class-hmc-utils.php' );
		
		/**
		 * Load dependecies managed by composer.
		 */
		require_once $this->dir_path.'includes/database/class-hmc-database.php';
		require_once $this->dir_path.'includes/api/class-hmc-restapi-category.php';
		require_once $this->dir_path.'includes/api/class-hmc-restapi-transaction.php';


		require_once( $this->dir_path.'admin/class-home-money-control-admin.php' );
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
			new Home_Money_Control_Admin($this);
		}


		$this->database_handler = new HMC_Database();
	}

	/**
	 * Load frontend CSS.
	 */
	private function enqueue_styles() {
		wp_register_style( __PNAMESPANE__ . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( __PNAMESPANE__ . '-frontend' );
	}

	/**
	 * Load frontend Javascript.
	 */
	private function enqueue_scripts() {
		wp_register_script( __PNAMESPANE__ . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( __PNAMESPANE__ . '-frontend' );
	}

	/**
	 * Load plugin localisation
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'home-money-control', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin textdomain.
	 */
	private function load_plugin_textdomain() {
		$domain = 'home-money-control';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Installation. Runs on activation.
	 */
	public function on_activation() {
		$this->database_handler->create();
		$this->database_handler->fill();
		update_option( __PNAMESPANE__ . '_version', $this->_version );
	}

	/**
	 * Deactivation Function
	 */
	public function on_deactivation() {
	}

	/**
	 * Uninstall Function
	 */
	public static function on_uninstall() {
		HMC_Database::DROP();
		delete_option( __PNAMESPANE__ . '_version' );
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	}


	/**
	 *
	 */
	public function run() {
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		$restApiCategory = new HMC_RestAPI_Category($this->database_handler->get_category_entity());
		$restApiTransaction = new HMC_RestAPI_Transaction($this->database_handler->get_category_entity(), $this->database_handler->get_transaction_entity() );


	}

}
