<?php


class MWF_CN_Post_Chatwork {

	public function __construct() {
	}

	public function _add_action( $form_key ) {
		add_action( 'mwform_before_send_admin_mail_' . $form_key, array( $this, '_post' ), 10, 2 );
	}

	/**
	 * @param $mail_admin MW_WP_Form_Mail
	 * @param $data       MW_WP_Form_Data
	 */
	public function _post( $mail_admin, $data ) {
		$form_id = $data->gets()['mw-wp-form-form-id'];

		if ( ! MWF_CN_Functions::is_send_chat( $form_id ) ) {
			return;
		}

		$room_id = MWF_CN_Functions::get_chatroom( $form_id );
		$token   = MWF_CN_Functions::get_api_token( $form_id );

		if ( ! $room_id || ! $token ) {
			return;
		}

		$this->post( $room_id, $token, $mail_admin );

		return;
	}

	public function post( $room, $token, $mail_data ) {

		$body = sprintf( '[info][title]%s[/title]%s[/info]', $mail_data->subject, $mail_data->body );


		$response = wp_remote_post(
			sprintf( 'https://api.chatwork.com/v2/rooms/%s/messages', $room ),
			array(
				'headers' => array(
					'X-ChatWorkToken' => $token,
				),
				'body'    => array(
					'body' => $body,
				),
			)
		);

		if ( ! is_wp_error( $response ) ) {
			$data = json_decode( $response['body'] );

			return $data;
		}

	}
}
