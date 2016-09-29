<?php

/**
 * My Home Finance.
 *
 * @package   Home_Money_Control_Admin
 * @author    Alessandro Staniscia <alessandro@staniscia.net>
 * @license   GPL-2.0+
 * @link      http://www.staniscia.net/home_money_control
 * @copyright 2014 Alessandro Staniscia
 *
 * @package Home_Money_Control_Admin
 * @author  Alessandro Staniscia <alessandro@staniscia.net>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Home_Money_Control_Admin
 */
class Home_Money_Control_Admin {


	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @param $owner    class   main class
	 */
	public function __construct( $owner ) {
		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = $owner;
		$this->plugin_slug = $plugin->plugin_slug;
		$this->version = $plugin->get_version();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );


		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
	}


	/**
	 * Register and enqueue admin-specific style sheets
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles( $hook ) {
		//$this->enqueue_styles( $hook );
		$this->apply_styles( $hook );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts( $hook ) {
		//$this->enqueue_scripts( $hook );
		$this->apply_scripts( $hook );
	}


	public function apply_styles( $hook ){

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix === $screen->id ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles');
		}

		if ( strpos( $hook, 'HMC' ) !== false ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles');
		}

		if ( strpos( $hook, 'HMC-id-menu-reports-list' ) !== false ) {
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( $this->plugin_slug . '-fullcalendar-style');
		}

	}
	

	public function apply_scripts($hook){

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( strpos( $hook, 'HMC' ) !== false ) {
			wp_enqueue_script( $this->plugin_slug . '-ajax-retry-script' );
			wp_enqueue_script( $this->plugin_slug . '-admin-base-script' );
		}

		if ( strpos( $hook, 'HMC-id-menu-counts' ) !== false ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-model-counts-script' );
			wp_enqueue_script( $this->plugin_slug . '-admin-views-counts-script' );
		}

		if ( strpos( $hook, 'HMC-id-menu-reports-list' ) !== false ) {
			wp_enqueue_script( $this->plugin_slug . '-moment-script' );
			wp_enqueue_script( $this->plugin_slug . '-fullcalendar-script' );
			wp_enqueue_script( $this->plugin_slug . '-chartjs' );
			wp_enqueue_script( $this->plugin_slug . '-admin-model-counts-script' );
			wp_enqueue_script( $this->plugin_slug . '-admin-views-report-script' );
		}

	}


	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Home Money Control - Admin Page', $this->plugin_slug ),
			__( 'Home Money Control', $this->plugin_slug ),
			'edit_posts',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

		add_menu_page(
			__( 'Home Money Control - Dashboard', $this->plugin_slug ),
			__( 'Home Money Control', $this->plugin_slug ),
			'edit_posts',
			$this->plugin_slug . '-id-root-menu',
			array( $this, 'display_dashboard_page' ),
			'dashicons-chart-area'
		);

		/* add_submenu_page(
			$this->plugin_slug . '-id-root-menu',
			__( 'Home Money Control - Dashboard', $this->plugin_slug ),
			__( 'Dashboard', $this->plugin_slug ),
			'edit_posts',
			$this->plugin_slug . '-id-menu-dashboard',
			array( $this, 'display_dashboard_page' )
		);*/

		add_submenu_page(
			$this->plugin_slug . '-id-root-menu',
			__( 'Home Money Control - Reports page', $this->plugin_slug ),
			__( 'Mese attuale', $this->plugin_slug ),
			'edit_posts',
			$this->plugin_slug . '-id-menu-reports-list',
			array( $this, 'display_reports_page' )
		);

		/*
				add_submenu_page(
				//$this->plugin_slug."-id-root-menu",
					$this->plugin_slug . "-id-menu-transaction-list",
					__( 'Add transaction', $this->plugin_slug ),
					__( 'Add transaction', $this->plugin_slug ),
					'edit_transaction',
					$this->plugin_slug . "-id-menu-transaction",
					array( $this, 'display_transaction_edit_page' )
				);
		*/


		add_submenu_page(
			$this->plugin_slug . '-id-root-menu',
			__( 'Home Money Control - Piano dei Conti', $this->plugin_slug ),
			__( 'Piano dei conti', $this->plugin_slug ),
			'edit_posts',
			$this->plugin_slug . '-id-menu-counts',
			array( $this, 'display_counts_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( __HMC_PATH__ . 'admin/views/admin.inc.php' );
	}

	/**
	 *
	 */
	public function display_dashboard_page() {
		include_once( __HMC_PATH__ . 'admin/views/dashboard.inc.php' );
	}


	/**
	 *
	 */
	public function display_counts_page() {
		include_once( __HMC_PATH__ . 'admin/views/counts.inc.php' );
	}


	/**
	 *
	 */
	public function display_reports_page() {
		include_once( __HMC_PATH__ . 'admin/views/reports.inc.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}


}
