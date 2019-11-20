<?php

class MWF_CN_Admin_Controller {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ) );

		$screen = get_current_screen();
		add_action( 'load-' . $screen->id, array( $this, 'save' ) );
		add_action( $screen->id, array( $this, 'render' ) );
	}

	public function admin_enqueue_script() {
		$url = plugins_url( MWF_CN_Config::NAME );

		wp_enqueue_style( 'select2', $url . '/select2/css/select2.min.css', array(), MWF_CN_Config::VERSION );
		wp_enqueue_script( 'select2', $url . '/select2/js/select2.full.min.js', array( 'jquery' ), MWF_CN_Config::VERSION, true );
		wp_enqueue_script( MWF_CN_Config::NAME . '-admin', $url . '/js/admin.js', array( 'jquery', 'select2' ), MWF_CN_Config::VERSION, true );
		wp_localize_script( MWF_CN_Config::NAME . '-admin', 'get_cn_user', array(
			'endpoint' => admin_url( 'admin-ajax.php' ),
			'action'   => 'get_chatwork_info',
		) );
	}

	public function render() {
		$functions = new MWF_CN_Functions();

		$settings = get_option( 'mwf_cn_general' );
		$forms    = get_option( 'mwf_cn_forms' );

		$api_token = '';
		$cn_name   = '';
		$room_id   = '';
		$rooms     = array();
		if ( is_array( $settings ) ) {

			$api_token = $settings['api_token'];
			$room_id   = $settings['room_id'];

			$cn_name = $functions->get_chatwork_user( $api_token );
			$rooms   = $functions->get_chatwork_room_list( $api_token );
		}

		$form_list = $functions->get_form_list();

		require plugin_dir_path( __FILE__ ) . '../../view/option_page.php';
	}

	public function save() {
		if ( ! isset( $_POST['_wpnonce'] ) ) {
			return;
		}
		if ( empty( $_POST['_wpnonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mwf-cn' ) ) {
			return;
		}

		$options = array(
			'api_token' => esc_html( $_POST['api_token'] ),
			'room_id'   => esc_html( $_POST['room_id'] ),
		);
		$result = update_option( 'mwf_cn_general', $options );

		if ( ! isset( $_POST['form_api_token'] ) ) {
			return;
		}

		$form_options = array();
		if ( isset( $_POST['form_send'] ) ) {
			foreach ( $_POST['form_send'] as $form_id => $value ) {
				$form_options[ $form_id ]['send'] = $value;
			}
		}

		foreach ( $_POST['form_room'] as $form_id => $value ) {
			$form_options[ $form_id ]['room'] = $value;
			$form_options[ $form_id ]['token'] = $_POST['form_api_token'][ $form_id ];
		}

		$result = update_option( 'mwf_cn_forms', $form_options );
	}

}
