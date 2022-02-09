<?php
require_once(WEMS_MAIL_SMTP_DIR.'init/easy-mail-smtp-functions.php');
if( isset($_POST['mail-send']) || isset( $_POST['email_test_nonce'] ) 
	|| wp_verify_nonce( isset($_POST['email_test_nonce']), 'email_test' ) ){

	$mail_settings = get_option( 'wems_mail_smtp_settings', array() );
	$settings = unserialize($mail_settings);
	
	add_action( 'phpmailer_init', 'wems_send_smtp_email' );
	
	$to = (isset($_POST['mail_to'])) ? sanitize_email($_POST['mail_to']) : '';
	$subject = 'Test Email Form WP Easy Mail SMTP';
	$message = (isset($_POST['mail_message'])) ? sanitize_text_field($_POST['mail_message']) : '';
	$headers = array('Content-Type: text/html; charset=UTF-8');
	$mail = wp_mail($to, $subject, $message, $headers);
	if($mail){
		?>
		<div class="notice notice-success is-dismissible"> 
			<p><strong>Mail sent successfully.</strong></p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
		<?php
	}else{
		?>
		<div class="notice notice-error is-dismissible">
			<?php if($GLOBALS['phpmailer']->ErrorInfo){ ?>
				<p><strong>Mail sending failed. <?php echo $GLOBALS['phpmailer']->ErrorInfo; ?></strong></p>
			<?php } ?>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
		<?php
	}
}
?>
<div class="wrap">
	<div class="mail-settings-content other-tab-section">
		<h1>Email Test</h1>
		<form method="post">
			<form method="post">
				<?php wp_nonce_field( 'email_test', 'email_test_nonce' ); ?>
				<table>
					<tr class="from-row">
						<td class="mail-form-label mail-form-title"><label for="mail-to-label">To: </label></td>
						<td><input type="text" name="mail_to" id="mail-to-label" class="mail-to txtbox mail-form-input"></td>
					</tr>
					<tr class="from-row">
						<td class="mail-message-label mail-form-title"><label for="mail-message-label">Message: </label></td>
						<td>
							<textarea name="mail_message" id="mail-message-label" class="mail-message txtbox mail-form-input"></textarea>
							<p class="description" id="tagline-description">No HTML tags allowed.</p>
						</td>
					</tr>
					<tr class="from-row">
						<td class="mail-form-label">
							<input type="submit" name="mail-send" class="mail-submit submit-btn" value="Send Mail">
						</td>
					</tr>
				</table>
			</form>	
		</form>
	</div>
</div>