<?php


class MWF_CN_Functions {

	public static function display_notice( $status, $str ) {
		if ( 'updated' === $status ) {
			self::success( $str );
		} elseif ( 'error' === $status ) {
			self::error( $str );
		}
	}

	public static function success( $str ) {
		printf( '<div class="success"><p>%s</p></div>', $str );
	}

	public static function error( $str ) {
		printf( '<div class="error"><p>%s</p></div>', $str );
	}

	public function _get_chatwork_info() {
		$token = $_POST['token'];

		$name = $this->get_chatwork_user( $token );

		$rooms = $this->get_chatwork_room_list( $_POST['token'] );

		$return_data = array(
			'name' => $name,
			'rooms' => $rooms,
		);
		echo json_encode( $return_data );

		die();
	}

	public function get_chatwork_user( $token ) {
		$response = wp_remote_get( 'https://api.chatwork.com/v2/me', array(
			'headers' => array(
				'X-ChatWorkToken' => $token,
			),
		) );

		if ( ! is_wp_error( $response ) ) {
			$data = json_decode( $response['body'] );

			return $data->name;
		}
	}

	public function get_chatwork_room_list( $token ) {
		$response = wp_remote_get( 'https://api.chatwork.com/v2/rooms', array(
			'headers' => array(
				'X-ChatWorkToken' => $token,
			),
		) );

		if ( ! is_wp_error( $response ) ) {
			$data = json_decode( $response['body'] );

			return $data;
		}
	}

	public function get_form_list() {
		$query = array(
			'post_type'      => 'mw-wp-form',
			'posts_per_page' => -1,
		);

		return new WP_Query( $query );
	}

	public static function is_send_chat( $form_key ) {
		$chat_settings = get_option( 'mwf_cn_general' );

		if ( ! $chat_settings ) {
			return false;
		}

		if ( ! is_array( $chat_settings ) ) {
			return false;
		}

		if ( ! isset( $chat_settings['room_id'] ) ) {
			return false;
		}

		$form_settings = get_option( 'mwf_cn_rooms' );

		if ( ! $form_settings ) {
			return false;
		}

		if ( ! is_array( $form_settings ) ) {
			return false;
		}

		if ( ! isset( $form_settings[ $form_key ]['send'] ) ) {
			return false;
		}

		if ( 'true' == $form_settings[ $form_key ]['send'] ) {
			return true;
		}

		return false;
	}

	public static function get_chatroom( $form_key ) {
		$form_settings = get_option( 'mwf_cn_rooms' );
		if ( isset( $form_settings[ $form_key ]['room'] ) && 'general' !== $form_settings[ $form_key ]['room'] ) {
			return $form_settings[ $form_key ]['room'];
		}

		$chat_settings = get_option( 'mwf_cn_general' );

		if ( isset( $chat_settings['room_id'] ) ) {
			return $chat_settings['room_id'];
		}

		return false;
	}

	public static function get_api_token( $form_key ) {
		$form_settings = get_option( 'mwf_cn_rooms' );
		if ( isset( $form_settings[ $form_key ]['token'] ) && ! empty( $form_settings[ $form_key ]['token'] ) ) {
			return $form_settings[ $form_key ]['token'];
		}

		$chat_settings = get_option( 'mwf_cn_general' );

		if ( isset( $chat_settings['api_token'] ) ) {
			return $chat_settings['api_token'];
		}

		return false;
	}

}
