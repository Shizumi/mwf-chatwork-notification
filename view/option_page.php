<?php

?>
<div class="wrap">
	<h2><?php _e( 'Chatwork Settings', 'mwf-chatwork-notification' ); ?></h2>
	<form method="post" action="">
		<?php wp_nonce_field( 'mwf-cn' ); ?>
		<h3><?php _e( 'General Settings', 'mwf-chatwork-notification' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="api_token"><?php _e( 'Chatwork API Token', MWF_CN_Config::DOMAIN ); ?></label>
				</th>
				<td>
					<input id="api_token" type="text" class="regular-text ltr" name="api_token" value="<?php echo $api_token; ?>">
					<p class="description"><?php _e( 'Enter Chatwork API token.', MWF_CN_Config::DOMAIN ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label><?php _e( 'Chatwork Name', MWF_CN_Config::DOMAIN ); ?></label>
				</th>
				<td>
					<p id="chatwork_name"><?php echo $cn_name; ?></p>
					<input type="hidden" name="cn_name" value="<?php echo $cn_name; ?>" id="cn_name_input">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="room_id"><?php _e( 'Chat Room', MWF_CN_Config::DOMAIN ); ?></label>
				</th>
				<td>
					<select name="room_id" id="room_id" class="room_select">
						<?php foreach ( $rooms as $room ) : ?>
							<option value="<?php echo $room->room_id; ?>" <?php selected( $room_id, $room->room_id ); ?>><?php echo $room->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</table>
		<hr>
		<input type="hidden" name="action" value="update">
		<h3>Form Setting</h3>
		<table class="form-table">
			<tr>
				<th scope="col">
					<p><?php _e( 'Form Name', MWF_CN_Config::DOMAIN ); ?></p>
				</th>
				<th scope="col">
					<p><?php _e( 'Chat Room', MWF_CN_Config::DOMAIN ); ?></p>
				</th>
				<th scope="col">
					<p><?php _e( 'Chatwork API Token', MWF_CN_Config::DOMAIN ); ?></p>
				</th>
				<th scope="col">
					<p><?php _e( 'Send', MWF_CN_Config::DOMAIN ); ?></p>
				</th>
			</tr>
			<?php if ( $form_list->have_posts() ) : ?>
				<?php while ( $form_list->have_posts() ) : ?>
					<?php $form_list->the_post(); ?>
					<tr>
						<th scope="row">
							<p><label for="form_<?php echo get_the_ID(); ?>"><?php the_title(); ?></label></p>
						</th>
						<td>
							<select name="form_room[<?php echo get_the_ID(); ?>]" id="form_<?php echo get_the_ID(); ?>" class="room_select">
								<option value="general" <?php selected( $room_id, 'general' ); ?>><?php _e( 'general', MWF_CN_Config::DOMAIN ); ?></option>
								<?php foreach ( $rooms as $room ) : ?>
									<option value="<?php echo $room->room_id; ?>" <?php // selected( $room_id, $room->room_id ); ?>><?php echo $room->name; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
						<td>
							<input id="form_api_token[<?php echo get_the_ID(); ?>]" type="text" class="regular-text ltr" name="form_api_token[<?php echo get_the_ID(); ?>]" value="<?php // echo $api_token; ?>" placeholder="<?php _e( 'Default is general settings.', MWF_CN_Config::DOMAIN ); ?>" data-form-id="<?php echo get_the_ID(); ?>">
						</td>
						<td>
							<p><input type="checkbox" class="regular-text ltr" name="form_send[<?php echo get_the_ID(); ?>]" value="true"<?php echo MWF_CN_Functions::is_send_chat( get_the_ID() ) ? 'checked' : ''; ?>></p>
						</td>
					</tr>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
</div>

