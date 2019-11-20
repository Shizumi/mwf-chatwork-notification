<?php
/**
 * Plugin Name: MW WP Form chatwork notification
 * Version: 0.0.1
 * Author: Shizumi
 * Author URI: https://blog.spicadots.com/
 * Created : November 16, 2019
 * Modified: November 16, 2019
 * Text Domain: mwf-chatwork-notification
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

include_once( plugin_dir_path( __FILE__ ) . 'classes/class-mwf-cn-functions.php' );
include_once( plugin_dir_path( __FILE__ ) . 'classes/class-mwf-cn-config.php' );

class MWF_CHATWORK_NOTIFICATION {

	/**
	 * MWF_CHATWORK_NOTIFICATION constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_init_files' ), 9 );
		add_action( 'plugins_loaded', array( $this, 'init' ), 11 );
	}

	public function  load_init_files() {
		require_once( plugin_dir_path( __FILE__ ) . 'classes/controllers/class-mwf-cn-admin-controller.php');
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-mwf-cn-post-chatwork.php');
	}

	/**
	 * Init
	 */
	public function init() {
		load_plugin_textdomain( MWF_CN_Config::DOMAIN );

		if ( ! $this->is_mw_wp_form_active() ) {
			add_action( 'admin_notices', array( $this, 'mw_wp_form_invalid' ) );

			return;
		}

		add_action( 'wp_ajax_get_chatwork_info', array( new MWF_CN_Functions, '_get_chatwork_info' ) );
		add_action( 'after_setup_theme', array( $this, 'mwf_cn_after_setup_theme' ), 12 );
		add_action( 'mwform_start_main_process', array( new MWF_CN_Post_Chatwork, '_add_action' ) );
	}

	public function mwf_cn_after_setup_theme() {
		if ( current_user_can( MWF_Config::CAPABILITY ) && is_admin() ) {
			add_action( 'admin_menu', array( $this, 'mwf_cn_admin_add_menu' ), 11 );
			add_action( 'current_screen', array( $this, 'mwf_cn_current_screen' ) );
		}
	}

	public function mwf_cn_admin_add_menu() {
		require_once( WP_PLUGIN_DIR . '/mw-wp-form/classes/config.php');

		add_submenu_page(
			'edit.php?post_type=' . MWF_Config::NAME,
			esc_html__( 'Chatwork Settings', MWF_CN_Config::DOMAIN ),
			esc_html__( 'Chatwork Settings', MWF_CN_Config::DOMAIN ),
			MWF_Config::CAPABILITY,
			MWF_CN_Config::SETTING_PAGE,
			'__return_false'
		);

		add_action( 'admin_init', array( $this, 'register_mwf_cn_settings_group' ) );
	}

	public function mwf_cn_current_screen( $screen ) {
		if ( $screen->id === MWF_Config::NAME . '_page_' . MWF_CN_Config::SETTING_PAGE ) {
			new MWF_CN_Admin_Controller();
		}
	}

	public function is_mw_wp_form_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		return is_plugin_active( 'mw-wp-form/mw-wp-form.php' );
	}

	/**
	 * MW WP Formが有効でない表示
	 */
	public function mw_wp_form_invalid() {
		MWF_CN_Functions::error( __( 'MW WP Form is invalid.', MWF_CN_Config::DOMAIN ) );
	}

	public function register_mwf_cn_settings_group() {
		register_setting( 'mwf_cn_settings', 'mwf_cn_general_settings' );
	}

}

new MWF_CHATWORK_NOTIFICATION();
