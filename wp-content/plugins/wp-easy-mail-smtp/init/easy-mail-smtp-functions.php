<?php
if(!function_exists('wems_custom_style')){
	function wems_custom_style(){
	    wp_enqueue_style( 'wems-main-style', WEMS_MAIL_SMTP_URL . 'css/easy-mail-smtp-style.css', array(), '1.0' );
	    wp_enqueue_script( 'wems-main-script', WEMS_MAIL_SMTP_URL . 'js/easy-mail-smtp-script.js', array(), '1.0' );
	}
}
add_action( 'admin_enqueue_scripts', 'wems_custom_style');

if(!function_exists('wems_add_mail_options_page')){
	function wems_add_mail_options_page()
	{
		add_menu_page(
			__('Easy Mail SMTP'),
			'Easy Mail SMTP',
			'manage_options',
			'wems_mail_options',
			'wems_mail_options',
			'',
			6
		);
	}
}
add_action( 'admin_menu', 'wems_add_mail_options_page' );

if(!function_exists('wems_mail_options')){
	function wems_mail_options(){
		?>
		<div class="mail-settings">
			<a href="<?php echo admin_url('admin.php?page=wems_mail_options&tab=wems_settings'); ?>">Settings</a>
			<a href="<?php echo admin_url('admin.php?page=wems_mail_options&tab=wems_email_test'); ?>">Email Test</a>
		</div>
		<?php
		if(isset($_GET['tab']) && $_GET['tab'] == 'wems_email_test'){
		    require_once(WEMS_MAIL_SMTP_DIR.'admin/easy-mail-smtp-tab-email-test.php');
		}else{
		    require_once(WEMS_MAIL_SMTP_DIR.'admin/easy-mail-smtp-tab-settings.php');
		}
	}
}

$mail_settings = get_option( 'wems_mail_smtp_settings', array() );
if($mail_settings){
	$settings = unserialize($mail_settings);
	if($settings['encryption'] == 465){
		$settings['encryption'] = "ssl";
	}elseif($settings['encryption'] == 587){
		$settings['encryption'] = "tls";
	}
	// SMTP Authentication
	define( 'WEMS_SMTP_USER',   $settings['username'] );    		// Username to use for SMTP authentication
	define( 'WEMS_SMTP_PASS',   $settings['password'] );       // Password to use for SMTP authentication
	define( 'WEMS_SMTP_HOST',   $settings['smtp_host'] );    // The hostname of the mail server
	define( 'WEMS_SMTP_FROM',   $settings['from'] ); 		// SMTP From email address
	define( 'WEMS_SMTP_NAME',   $settings['from_name'] );    // SMTP From name
	define( 'WEMS_SMTP_PORT',   $settings['smtp_port'] );                  // SMTP port number - likely to be 25, 465 or 587
	define( 'WEMS_SMTP_SECURE', $settings['encryption'] );                 // Encryption system to use - ssl or tls
	define( 'WEMS_SMTP_AUTH',    true );                 // Use SMTP authentication (true|false)
	define( 'WEMS_SMTP_DEBUG',   1 );                    // for debugging purposes only set to 1 or 2
}

if(!function_exists('wems_send_smtp_email')){
	function wems_send_smtp_email( PHPMailer $phpmailer ){
		$phpmailer->isSMTP();
		$phpmailer->Host       = WEMS_SMTP_HOST;
		$phpmailer->SMTPAuth   = true;
		$phpmailer->Port       = WEMS_SMTP_PORT;
		$phpmailer->Username   = WEMS_SMTP_USER;
		$phpmailer->Password   = WEMS_SMTP_PASS;
		$phpmailer->SMTPSecure = WEMS_SMTP_SECURE;
		$phpmailer->From       = WEMS_SMTP_FROM;
		$phpmailer->FromName   = WEMS_SMTP_NAME;     
	}
}
add_action( 'phpmailer_init', 'wems_send_smtp_email' );