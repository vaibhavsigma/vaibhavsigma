<?php
if( isset($_POST['mail-submit']) || isset( $_POST['email_settings_nonce'] ) 
	|| wp_verify_nonce( isset($_POST['email_settings_nonce']), 'email_settings' ) )
{
	$from = (isset($_POST['mail_from'])) ? sanitize_text_field($_POST['mail_from']) : '';
	$from_nm = (isset($_POST['mail_from_nm'])) ? sanitize_text_field($_POST['mail_from_nm']) : '';
	$smtp_host = (isset($_POST['mail_smtp_host'])) ? sanitize_text_field($_POST['mail_smtp_host']) : '';
	$encrypt = (isset($_POST['mail_smtp_encrypt'])) ? sanitize_text_field($_POST['mail_smtp_encrypt']) : '';
	$smtp_port = isset($_POST['mail_smtp_port']) ? sanitize_text_field($_POST['mail_smtp_port']) : '';
	$username = isset($_POST['mail_uname']) ? sanitize_text_field($_POST['mail_uname']) : '';
	$password = isset($_POST['mail_pass']) ? sanitize_text_field($_POST['mail_pass']) : '';

	$arr_mail_smtp = array('from' => $from, 'from_name' => $from_nm, 'smtp_host' => $smtp_host, 'encryption' => $encrypt, 'smtp_port' => $smtp_port, 'username' => $username, 'password' => $password);
	$str_mail_smtp = serialize($arr_mail_smtp);
	update_option( 'wems_mail_smtp_settings', $str_mail_smtp );
	?>
	<div class="notice notice-success is-dismissible"> 
		<p><strong>Settings Saved.</strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text">Dismiss this notice.</span>
		</button>
	</div>
	<?php
}
$mail_settings = get_option( 'wems_mail_smtp_settings', array() );
$settings = unserialize($mail_settings);
?>
<h1 class="mailbox-title">WP Easy Mail SMTP Settings</h1>
<div class="wrap">
	<div class="mail-settings-content">
		<form method="post">
			<?php wp_nonce_field( 'email_settings', 'email_settings_nonce' ); ?>
			<table class="table table-responsive">
				<tr class="from-row">
					<td class="mail-form-label mail-form-title"><label for="mail-from-label">From: </label></td>
					<td><input type="text" name="mail_from" id="mail-from-label" class="mail-from txtbox mail-form-input" value="<?php echo esc_html_e($settings['from'], 'wems_mail_smtp'); ?>"></td>
				</tr>
				<tr class="from-row">
					<td class="mail-form-label mail-form-title"><label for="mail-from-nm-label">From Name: </label></td>
					<td><input type="text" name="mail_from_nm" id="mail-from-nm-label" class="mail-from-nm txtbox mail-form-input" value="<?php echo esc_html_e($settings['from_name'], 'wems_mail_smtp'); ?>"></td>
				</tr>
				<tr class="smtp-row">
					<td class="mail-form-label mail-form-title"><label for="mail-smtp-host-label">SMTP Host: </label></td>
					<td><input type="text" name="mail_smtp_host" id="mail-smtp-host-label" class="mail-smtp-host txtbox mail-form-input" value="<?php echo esc_html_e($settings['smtp_host'], 'wems_mail_smtp'); ?>"></td>
				</tr>
				<tr class="from-row">
					<td class="mail-form-label mail-form-title"><label for="mail-smtp-encrypt-label">Type Of Encryption: </label></td>
					<td>
						<div class="mail-form-radio">
							<input type="radio" name="mail_smtp_encrypt" value="465" <?php if($settings['encryption'] == '465'){ echo 'checked'; }else{ echo ''; } ?>>
							<span>SSL</span>
						</div class="mail-form-radio">
						<div>
							<input type="radio" name="mail_smtp_encrypt" value="587" <?php if($settings['encryption'] == '587'){ echo 'checked'; }else{ echo ''; } ?>>
							<span>TLS</span>
						</div>
						
					</td>
				</tr>
				<tr class="from-row">
					<td class="mail-form-label mail-form-title"><label for="mail-smtp-port-label">SMTP Port: </label></td>
					<td><input type="text" name="mail_smtp_port" id="mail-smtp-port-label" class="mail-smtp-port txtbox mail-form-input" value="<?php echo esc_html_e($settings['encryption'], 'wems_mail_smtp'); ?>"></td>
				</tr>
				<tr class="from-row">
					<td class="mail-form-label mail-form-title"><label for="mail-username-label">Username: </label></td>
					<td><input type="text" name="mail_uname" id="mail-username-label" class="mail-username txtbox mail-form-input" value="<?php echo esc_html_e($settings['username'], 'wems_mail_smtp'); ?>"></td>
				</tr>
				<tr class="from-row">
					<td class="mail-form-label mail-form-title"><label for="mail-password-label">Password: </label></td>
					<td><input type="password" name="mail_pass" id="mail-password-label" class="mail-password txtbox mail-form-input" value="<?php echo esc_html_e($settings['password'], 'wems_mail_smtp'); ?>"></td>
				</tr>
				<tr class="from-row">
					<td class="mail-form-label"><input type="submit" name="mail-submit" class="mail-submit submit-btn" value="Save Changes"></td>
				</tr>
			</table>
		</form>
	</div>
</div>