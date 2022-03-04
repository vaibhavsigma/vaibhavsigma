<?php

global $versionConfigData;
global $isExtended;
global $partner_theme_config;
global $pppRequiredDocument;
$isExtended = '0';

$versionConfigData = readJson(PRIME_RATES_LOAN_APPLICATIONS_JSON_DIR.'ELA_ConfigurationV001.json');
$pppRequiredDocument = readJson(PRIME_RATES_LOAN_APPLICATIONS_JSON_DIR.'PPPrequiredDocuments.json');


require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-ppp-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-sme-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-auto-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-uk-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-payday-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'payday-offers-page.php');
require_once(plugin_dir_path( __FILE__ ) . 'los-lead-aggregator-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'los-lead-aggregator-offers-page.php');
require_once(plugin_dir_path( __FILE__ ) . 'uk-creditcard-offers-page.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-uk-creditcard.php');
require_once(plugin_dir_path( __FILE__ ) . 'uk-creditcard-offers-page.php');
require_once(plugin_dir_path( __FILE__ ) . 'gds-offers-page.php');
require_once(plugin_dir_path( __FILE__ ) . 'gds-creditcard-offers-page.php');
require_once(plugin_dir_path( __FILE__ ) . 'gds-creditcard.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-extended-applications.php');

//echo '<pre>';print_r($configData);


function create_borrowers(){
	global $isExtended,$configData,$partner_theme_config;
	global $versionConfigData,$bank_slug;
	if(!session_id()){@session_start();}
	
  	if (isset( $_POST["wpcustom_user_email"] )) {
		$user_login		= $_POST["wpcustom_user_email"]."_".$_POST['wpcustom_user_bank_slug'];	
		$user_email		= $_POST["wpcustom_user_email"];
		$user_fname		= $_POST["wpcustom_user_fname"];
		$user_uuid		= $_POST["wpcustom_user_uuid"];
		$user_pass		= $_POST["wpcustom_user_pass"];
		$pass_confirm 	= $_POST["wpcustom_user_pass_confirm"];
 
		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');
 
		if(username_exists($user_login)) {
			// Username already registered
			mycustom_errors()->add('username_unavailable', __('Username already taken'));
		}
		if(!validate_username($user_login)) {
			// invalid username
			mycustom_errors()->add('username_invalid', __('Invalid username'));
		}
		if($user_login == '') {
			// empty username
			mycustom_errors()->add('username_empty', __('Please enter a username'));
		}
		/*if(!is_email($user_email)) {
			//invalid email
			mycustom_errors()->add('email_invalid', __('Invalid email'));
		}*/
		
		if($user_pass == '') {
			// passwords do not match
			mycustom_errors()->add('password_empty', __('Please enter a password'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			mycustom_errors()->add('password_mismatch', __('Passwords do not match'));
		}
 
		$errors = mycustom_errors()->get_error_messages();
//exit('outside loop3');		
		// only create the user in if there are no errors
		if(empty($errors)) {
 
			$new_user_id = wp_insert_user(array(
					'user_login'		=> $user_login,
					'user_pass'	 		=> $user_pass,
					'user_email'		=> $user_login,
					'first_name'		=> $user_fname,
					'description'		=> $user_uuid,
					'user_registered'	=> date('Y-m-d H:i:s'),
					'role'				=> 'subscriber'
				)
			);
			if($new_user_id && !is_wp_error( $new_user_id )) {
				$isExtended = '1';
				$borrower_curl = New Curl();
				$dvConfigData = (object)$configData['dv360'];
				$url=$dvConfigData->mock_url;
				$data['TransactionType'] = "getBorrowerInfo";
				$result = $borrower_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
				$borrower_json = json_encode($result->Response);
				$_SESSION['extendedAppData'] = $borrower_json;
				
				$applcation_curl = New Curl();
				$dvConfigData = (object)$configData['dv360'];
				$url=$dvConfigData->mock_url;
				$data['TransactionType'] = "smeCheckoffDocuments";
				$applcation_result = $applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
				$applcation_json = json_encode($applcation_result->Response); 
				//echo count($applcation_result->Response->Applications);exit('call');
				$_SESSION['sme_document_data'] = $applcation_json;
				//setcookie('sme_document_data',$applcation_json,0,'/','api-dataview360.com');
				// send an email to the admin alerting them of the registration
				//wp_new_user_notification($new_user_id);
				
				$code = sha1( $new_user_id . time() );
				$_SESSION['user_code'] = $code;
				$_SESSION['user_id'] = $new_user_id;
				
				add_user_meta( $new_user_id, 'has_to_be_activated', $code, true );
				
				/*$activation_link = add_query_arg( array( 'key' => $code, 'user' => $new_user_id, 'application_type' => 'SME' ), get_permalink( 5445 ));
				add_user_meta( $new_user_id, 'has_to_be_activated', $code, true );
				// Send verification email via Mandrill
				$verify_email = new MandrillHandler();
				$user_data = array();
				$user_data['firstname'] = $user_fname;
				$user_data['email'] = $user_email;
				$user_data['activation_link'] = $activation_link;
				$response = $verify_email->scheduleEmailVerification($user_data);*/
				
				// log the new user in
				wp_setcookie($user_login, $user_pass, true);
				wp_set_current_user($new_user_id, $user_login);	
				do_action('wp_login', $user_login);
				$result = array();
				$result['status'] = 'success';
				$result['message'] = array('User created successfully.');
				echo json_encode($result);
				exit;
			}else{
				$result = array();
				$result['status'] = 'error';
				$result['message'] = array('Error creating user.');
				echo json_encode($result);
				exit;
			}
 
		}else{
			$result = array();
			$result['status'] = 'error';
			$result['message'] = $errors;
			echo json_encode($result);
			exit;
		}
 
	}
}

add_action( 'wp_ajax_gds_los_mandrill_complete', 'gds_los_mandrill_complete' );
add_action( 'wp_ajax_nopriv_gds_los_mandrill_complete', 'gds_los_mandrill_complete' );
add_action( 'wp_ajax_create_borrowers', 'create_borrowers' );
add_action( 'wp_ajax_nopriv_create_borrowers', 'create_borrowers' );	

// user registration login form
function mycustom_registration_form() {
	// Send the thank-you email variable for Mandrill
	if(isset($_POST['email'])){
		$objMandrill = New MandrillHandler();
		cancelScheduledEmail($objMandrill);
		$mandrill_data = array();
		$mandrill_data['email'] = $_POST['email'];
		$mandrill_data['firstname'] = $_POST['fName'];
		$objMandrill->scheduleThankYouEmail($mandrill_data);
	}
	// only show the registration form to non-logged-in members
	if(!is_user_logged_in()) {
 
		global $mycustom_load_css, $isExtended,$partner_theme_config,$versionConfigData;
		$_SESSION['theme_configuration'] = $partner_theme_config;
		$_SESSION['loan_amount'] = @$_POST['loanamount'];
		// set this to true so the CSS is loaded
		$mycustom_load_css = true;
		$user_login = @$_SESSION['borrower_email']."_".$partner_theme_config['slug'];
		if(username_exists($user_login) && $_SESSION['borrower_email'] != '') {
			$output = mycustom_login_form_fields();
		}else{
			// check to make sure user registration is enabled
			$registration_enabled = get_option('users_can_register');
	 
			// only show the registration form if allowed
			if($registration_enabled) {
				$output = mycustom_registration_form_fields();
			} else {
				$output = __('User registration is not enabled');
			}
		}
		return $output;
	}else{
		wp_redirect("https://".$_SERVER["HTTP_HOST"]."/borrower-login"); exit;
	}
}
add_shortcode('register_form', 'mycustom_registration_form');

// user login form
function mycustom_login_form() {
 
	if(!is_user_logged_in()) {
 
		global $mycustom_load_css, $isExtended,$partner_theme_config,$versionConfigData;
 
		// set this to true so the CSS is loaded
		$mycustom_load_css = true;
 
		$output = mycustom_login_form_fields();
	} else {
		wp_redirect("https://".$_SERVER["HTTP_HOST"]."/application-status"); exit;
	}
	return $output;
}
add_shortcode('login_form', 'mycustom_login_form');


function mycustom_reset_password_form(){
	if(!is_user_logged_in()) {
 
		global $mycustom_load_css, $isExtended,$partner_theme_config,$versionConfigData;
		if(isset($_GET['key']) && isset($_GET['user']))
		{	
			$output = mycustom_reset_password_form_fields();
		}else{
			$output = 'Query parameters are missing';
		}
	} else {
		wp_redirect("https://".$_SERVER["HTTP_HOST"]."/application-status"); exit;
	}
	return $output;
}
add_shortcode('reset_password_form', 'mycustom_reset_password_form');


// registration form fields
function mycustom_registration_form_fields() {
	global $partner_theme_config,$versionConfigData;
		
	ob_start(); ?>	
		<h3 class="mycustom_header"><?php echo $partner_theme_config['login_header']; ?></h3>
		<h4 class="mycustom_header"><?php echo $partner_theme_config['login_header_bottom']; ?></h4>
 
		
		<?php /*if(isset($_POST['mycustom_register_nonce'])){ ?>
			<div id="myModal" class="modal displayblock">
				<div class="modal-content">
					<div class="wrapper loans">
						<div class="trans-logo">
							<img src="/wp-content/themes/prime-rates-gdsbank/images/gds-link-logo.png">
						</div>
						<h3>Thank you!</h3> 
						<h4>Your account has been created.</h4>
						<h4>We are sending an email to your inbox with a link to verify your account. In order to access your account in the future, you will need to click the link in that email to verify the account (you do not need to do it at this time).</h4>
						<a href="/los-full-application/app-page-1" class="btn bc-primary">Continue with Application</a>
					</div>
				</div>
			</div>
		<?php }else{*/ ?>
		<form id="mycustom_registration_form" class="mycustom_form" action="" method="POST">
			<div id="breadcrumb-preloader-preloader">
			  <div id="preloader" class=" box_item ">
			  <?php
			// show any error messages after form submission
			mycustom_show_error_messages(); ?>
				 <div class="">
					<div class="row">
					    <div class=" text-control-align col-sm-12 col-xs-12 ">
							<div class="form-group ">
								<div class="">
								<div class="col-sm-4 user-lable"><h4 style="margin: 8px 0;padding-left: 25px;font-weight: 400;">Email:</h4></div>
								<div class="col-sm-8 email-placeholder">
								<?php if(isset($_SESSION['borrower_email']) && $_SESSION['borrower_email'] != ''){ echo $_SESSION['borrower_email']; ?>
									<input name="mycustom_user_email" value="<?php echo $_SESSION['borrower_email']; ?>" type="hidden" class="form-control number">
								<?php }else if(isset($_POST['email']) && $_POST['email'] != ''){ echo $_POST['email']; ?>
									<input name="mycustom_user_email" value="<?php echo $_POST['email']; ?>" type="hidden" class="form-control number">
								<?php }else{ ?>
									<input name="mycustom_user_email" value="" type="text" required id="email" class="form-control number">
								<?php } ?>
								</div>
								</div>
							</div>
							<div class="form-group ">
								<!--<a class="loan-app info-tooltip"><span class="tooltiptext tooltip-top-margin-createborrower"><?php echo $partner_theme_config['login_tooltip']; ?></span><i class="fa fa-question-circle"></i></a>-->
								<h3 class="error-heading">Please enter a password with:</h3>
								  <div id="message">
								  <p id="length" class="invalid">At least <b>8 characters</b></p>
								  <p id="letter" class="invalid">At least one <b>lowercase</b> letter</p>
								  <p id="capital" class="invalid">At least one <b>uppercase</b> letter</p>
								  <p id="number" class="invalid">At least one <b>number</b></p>
								  <p id="special" class="invalid">At least one <b>special</b> character</p>
								</div>
								<input name="mycustom_user_pass" placeholder="Password" required type="password"  pattern="(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}" id="password" class="form-control number">
							</div>
							<div class="form-group ">
								<!--<label for="password_again">Enter same password again</label>-->
								<input name="mycustom_user_pass_confirm" placeholder="Verify Password" type="password" required id="password_again"  class="form-control number">
								<div id="divCheckPassword">&nbsp;</div>
							</div>
						</div>
					</div>	
					<div class="row">
						<input type="hidden" name="mycustom_user_fname" value="<?php echo @$_POST['fName']; ?>"/>
						<input type="hidden" name="mycustom_user_bank_slug" value="<?php echo @$partner_theme_config['slug']; ?>"/>
						<input type="hidden" name="mycustom_user_uuid" value="<?php echo @$_POST['uuid']; ?>"/>
						<input type="hidden" name="mycustom_register_nonce" value="<?php echo wp_create_nonce('mycustom-register-nonce'); ?>"/>
					    <div class="col-sm-12 col-xs-12 button-control-align  ">
							<button type="submit" id="createpassword" name="createpassword" class=" btn bc-primary ">Continue</button>
					    </div>
					</div>
				</div>
			  </div>
		    </div>
		</form>
		<?php //} ?>
	<?php
	return ob_get_clean();
}

// login form fields
function mycustom_login_form_fields() {
		global $partner_theme_config,$versionConfigData;
		//print_r($_SESSION['theme_configuration']);exit('call123');
		ob_start(); ?>
		
 
		<div id="breadcrumb-preloader-preloader">
		  <div id="preloader" class=" box_item ">
		  <?php
			// show any error messages after form submission
			mycustom_show_error_messages(); ?>
			 <div class="">
				<?php if(isset($_GET['verified']) && $_GET['verified'] == 'false' && !isset($_POST['mycustom_resend_nonce'])){ ?>
					<div class=" text-control-align col-sm-12 col-xs-12 ">
						<h3 class="mycustom_header">Your account has yet to be verified. </h3><h4>We previously sent you an email with a verification link (please check your Inbox and Junk Mailbox).</h4>
						<form action="" method="POST">
							<input type="hidden" name="mycustom_user_email" value="<?php echo @$_SESSION['borrower_email']; ?>">
							<input type="hidden" name="mycustom_resend_nonce" value="<?php echo wp_create_nonce('mycustom-resend-nonce'); ?>"/>
							<button type="submit" class="btn bc-primary">Resend Verification Email</button>
						</form>
					</div>
				<?php }else if(isset($_POST['mycustom_resend_nonce'])){ ?>
					<div class=" text-control-align col-sm-12 col-xs-12 ">
						<h3 class="mycustom_header">Verification email is resent.</h3><h4>Please check your email address and activate your account.</h4>
					</div>
				<?php }else if(isset($_GET['verified']) && $_GET['verified'] == 'true'){ ?>
					<form id="mycustom_login_form"  class="mycustom_form"action="" method="post">
					<h3 style="text-align:center;" class="mycustom_header"><?php _e('Please enter your password to continue'); ?></h3>
						<div class="row">
							<div class=" text-control-align col-sm-12 col-xs-12 ">
								<div class="form-group ">
									<input name="mycustom_user_email_label" placeholder="Email" id="mycustom_user_email" value="<?php echo @$_SESSION['borrower_email']; ?>" disabled class="form-control" type="text"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class=" text-control-align col-sm-12 col-xs-12 ">
								<div class="form-group ">
									<input name="mycustom_user_pass" required placeholder="Password" id="mycustom_user_pass" class="form-control" type="password"/>
								</div>
							</div>
						</div>	
						<div class="row">
							<div class="col-sm-12 col-xs-12 button-control-align  ">
								<input type="hidden" name="mycustom_login_nonce" value="<?php echo wp_create_nonce('mycustom-login-nonce'); ?>"/>
								<input type="hidden" name="mycustom_user_email" value="<?php echo @$_SESSION['borrower_email']; ?>"/>
								<button type="submit" id="mycustom_login_submit" name="login" class=" btn bc-primary ">Continue</button>
								<a style="display:block;" href="#" onclick="showForgotPassword();">Forgot my Password</a>
							</div>
						</div>
					</form>
					<form id="forgot_password_form"  class="mycustom_form" action="" method="post" style="display:none;">
					<h3 style="text-align:center;" class="mycustom_header"><?php _e('Forgot Password'); ?></h3>
						<div class="row">
							<div class=" text-control-align col-sm-12 col-xs-12 ">
								<div class="form-group ">
									<input name="mycustom_user_email" value="<?php echo @$_SESSION['borrower_email']; ?>" placeholder="Email" id="mycustom_user_email" required class="form-control" type="text"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 col-xs-12 button-control-align  ">
								<input type="hidden" name="mycustom_forgot_nonce" value="<?php echo wp_create_nonce('mycustom-forgot-nonce'); ?>"/>
								<button type="submit" id="mycustom_forgot_submit" name="login" class=" btn bc-primary ">Submit</button>
							</div>
						</div>
					</form>
				<?php }else if(isset($_POST['mycustom_forgot_nonce'])){ ?>
					<div class="row">
						<div class=" text-control-align col-sm-12 col-xs-12 ">
							<h3 class="mycustom_header">Please check your email for password reset link!!</h3>
						</div>
					</div>
				<?php }else{ ?>
					<form id="mycustom_login_form"  class="mycustom_form"action="" method="post">
					<h3 style="text-align:center;" class="mycustom_header"><?php _e('Please Login to your account'); ?></h3>
						<div class="row">
							<div class=" text-control-align col-sm-12 col-xs-12 ">
								<div class="form-group ">
									<input name="mycustom_user_email" required placeholder="Email" id="mycustom_user_email" value="<?php echo @$_POST['email']; ?>" class="form-control" type="text"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class=" text-control-align col-sm-12 col-xs-12 ">
								<div class="form-group ">
									<input name="mycustom_user_pass" required placeholder="Password" id="mycustom_user_pass" class="form-control" type="password"/>
								</div>
							</div>
						</div>	
						<div class="row">
							<div class="col-sm-12 col-xs-12 button-control-align  ">
								<input type="hidden" name="mycustom_login_nonce" value="<?php echo wp_create_nonce('mycustom-login-nonce'); ?>"/>
								<button type="submit" id="mycustom_login_submit" name="login" class=" btn bc-primary ">Continue</button>
								<a style="display:block;" href="#" onclick="showForgotPassword();">Forgot my Password</a>
							</div>
						</div>
					</form>
					<form id="forgot_password_form"  class="mycustom_form" action="" method="post" style="display:none;">
					<h3 style="text-align:center;" class="mycustom_header"><?php _e('Forgot Password'); ?></h3>
						<div class="row">
							<div class=" text-control-align col-sm-12 col-xs-12 ">
								<div class="form-group ">
									<input name="mycustom_user_email" required placeholder="Email" id="mycustom_user_email" class="form-control" type="text"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 col-xs-12 button-control-align  ">
								<input type="hidden" name="mycustom_forgot_nonce" value="<?php echo wp_create_nonce('mycustom-forgot-nonce'); ?>"/>
								<button type="submit" id="mycustom_forgot_submit" name="login" class=" btn bc-primary ">Submit</button>
							</div>
						</div>
					</form>
				<?php } ?>
			</div>
		  </div>
		</div>
		
	<?php
	return ob_get_clean();
}

// Reset Password form fields
function mycustom_reset_password_form_fields() {
		global $partner_theme_config,$versionConfigData;
		//print_r($_SESSION['theme_configuration']);exit('call123');
		ob_start(); ?>
		<h3 class="mycustom_header"><?php _e('Reset Password'); ?></h3>
 
		<div id="breadcrumb-preloader-preloader">
		  <div id="preloader" class=" box_item ">
			 <?php
			// show any error messages after form submission
			mycustom_show_error_messages(); ?>
			 <div class="">
				<?php if(isset($_POST['mycustom_reset_nonce'])){ ?>
					<div class="row">
						<div class=" text-control-align col-sm-12 col-xs-12 ">
							<h3 class="mycustom_header">Your password is reset successfully!! Please click on below button to login.</h3>
							<a href="/borrower-login" class="btn btn-primary">Login</a>
						</div>
					</div>
				<?php }else{ ?>
				 <form id="mycustom_login_form"  class="mycustom_form"action="" method="post">
					<div class="row">
					    <div class=" text-control-align col-sm-12 col-xs-12 ">
							<div class="form-group ">
								<h3 class="error-heading">Please enter a password with:</h3>
								  <div id="message">
								  <p id="length" class="invalid">At least <b>8 characters</b></p>
								  <p id="letter" class="invalid">At least one <b>lowercase</b> letter</p>
								  <p id="capital" class="invalid">At least one <b>uppercase</b> letter</p>
								  <p id="number" class="invalid">At least one <b>number</b></p>
								  <p id="special" class="invalid">At least one <b>special</b> character</p>
								</div>
								<input name="mycustom_user_pass" placeholder="Password" required type="password"  pattern="(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z]).{8,}" id="password" class="form-control number">
							</div>
							<div class="form-group ">
								<!--<label for="password_again">Enter same password again</label>-->
								<input name="mycustom_user_pass_confirm" placeholder="Verify Password" type="password" required id="password_again"  class="form-control number">
								<div id="divCheckPassword">&nbsp;</div>
							</div>
						</div>
					</div>	
					<div class="row">
						<input type="hidden" name="mycustom_user_code" value="<?php echo @$_GET['key']; ?>"/>
						<input type="hidden" name="mycustom_user_id" value="<?php echo @$_GET['user']; ?>"/>
						<input type="hidden" name="mycustom_reset_nonce" value="<?php echo wp_create_nonce('mycustom-reset-nonce'); ?>"/>
					    <div class="col-sm-12 col-xs-12 button-control-align  ">
							<button type="submit" id="createpassword" name="createpassword" class=" btn bc-primary ">Continue</button>
					    </div>
					</div>
				</form>
				<?php } ?>
			</div>
		  </div>
		</div>
		
	<?php
	return ob_get_clean();
}

// logs a member in after submitting a form
function mycustom_login_member() {
	global $partner_theme_config,$configData;
	if(!session_id()){@session_start();}
	if(isset($_POST['mycustom_user_email']) && wp_verify_nonce($_POST['mycustom_login_nonce'], 'mycustom-login-nonce')) {
		$user_login = $_POST['mycustom_user_email']."_".$partner_theme_config['slug'];
		// this returns the user ID and other info from the user name
		$user = get_user_by('login',$user_login);
		//print_r($user);exit;
		if(!$user) {
			// if the user name doesn't exist
			mycustom_errors()->add('empty_username', __('Invalid username'));
		}
 
		if(!isset($_POST['mycustom_user_pass']) || $_POST['mycustom_user_pass'] == '') {
			// if no password was entered
			mycustom_errors()->add('empty_password', __('Please enter a password'));
		}
 
		// check the user's login with their password
		if(!wp_check_password($_POST['mycustom_user_pass'], $user->user_pass, $user->ID)) {
			// if the password is incorrect for the specified user
			mycustom_errors()->add('empty_password', __('Incorrect password'));
		}
		
		// check for email account activation
		if ( get_user_meta( $user->ID, 'has_to_be_activated', true ) != false ) {
			mycustom_errors()->add('activation_failed', __('Please activate your account using the link in the email we sent.'));
		}
 
		// retrieve all error messages
		$errors = mycustom_errors()->get_error_messages();
 
		// only log the user in if there are no errors
		if(empty($errors)) {
 
			$isExtended = '1';
			$borrower_curl = New Curl();
			$dvConfigData = (object)$configData['dv360'];
			//$url=$dvConfigData->post_url;
			//$url= "https://" . $_SERVER['HTTP_HOST'] . $dvConfigData->post_url;
			$url= "https://" . $_SERVER['HTTP_HOST'] . "/DV360MockNew.php";
			$data['TransactionType'] = "getBorrowerInfo";
			$result = $borrower_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
			$borrower_json = json_encode($result->Response);
			$_SESSION['extendedAppData'] = $borrower_json;
			
			$sme_applcation_curl = New Curl();
			$dvConfigData = (object)$configData['dv360'];
			$url=$dvConfigData->mock_url;
			$data['TransactionType'] = "smeCheckoffDocuments";
			$applcation_result = $sme_applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
			$applcation_json = json_encode($applcation_result->Response); 
			//echo count($applcation_result->Response->Applications);exit('call');
			$_SESSION['sme_document_data'] = $applcation_json;
			//setcookie('sme_document_data',$applcation_json,0,'/','api-dataview360.com');
			$applcation_curl = New Curl();
			$dvConfigData = (object)$configData['dv360'];
			//$url=$dvConfigData->post_url;
			//$url= "https://" . $_SERVER['HTTP_HOST'] . $dvConfigData->post_url;
			$data['TransactionType'] = "LOSBorrowerStatus";
			$applcation_result = $applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
			$applcation_json = json_encode($applcation_result->Response);
			//echo count($applcation_result->Response->Applications);exit('call');
			$_SESSION['application_data'] = $applcation_json;
				
			wp_setcookie($user_login, $_POST['mycustom_user_pass'], true);
			wp_set_current_user($user->ID, $user_login);	
			do_action('wp_login', $user_login);
			if(isset($_COOKIE['isSME']) && $_COOKIE['isSME'] == 1){
				$borrower_curl = New Curl();
				$dvConfigData = (object)$configData['dv360'];
				$url=$dvConfigData->mock_url;
				$data['TransactionType'] = "getBorrowerInfo";
				$result = $borrower_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
				$borrower_json = json_encode($result->Response);
				$_SESSION['extendedAppData'] = $borrower_json;
				
				$sme_applcation_curl = New Curl();
				$dvConfigData = (object)$configData['dv360'];
				$url=$dvConfigData->mock_url;
				$data['TransactionType'] = "smeCheckoffDocuments";
				$applcation_result = $sme_applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
				$applcation_json = json_encode($applcation_result->Response); 
				//echo $applcation_json;exit('call');
				$_SESSION['sme_document_data'] = $applcation_json;
				//setcookie('sme_document_data',$applcation_json,0,'/','api-dataview360.com');
				$applcation_curl = New Curl();
				$dvConfigData = (object)$configData['dv360'];
				$url=$dvConfigData->mock_url;
				$smeData = array();
				$smeData['TransactionType'] = 'retrieveOffers';
				$sme_applcation_result = $applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$smeData));
				$_SESSION['isOfferAvailable'] = 1;
				$_SESSION[$session_id] = $sme_applcation_result;
				$_SESSION[session_id().'-formData'] = $smeData;
				$_SESSION['offerAvailableArray'] = array_merge($_SESSION['offerAvailableArray'],array(session_id()));
				wp_redirect("https://".$_SERVER["HTTP_HOST"]."/personal-loans/pre-qualify/offers");exit;
			}
			// send the user to Application status page if it has more than one application
			if(count($applcation_result->Response->Applications) > 1){
				wp_redirect("https://".$_SERVER["HTTP_HOST"]."/application-status"); exit;
			}else{
				if($applcation_result->Response->Applications[0]->Status == "Offer Selection Pending"){
					wp_redirect("https://".$_SERVER["HTTP_HOST"]."/personal-loans/pre-qualify/offers"); exit;
				}else if($applcation_result->Response->Applications[0]->Status == "Full Application Pending"){
					wp_redirect("https://".$_SERVER["HTTP_HOST"]."/los-full-application/app-page-1"); exit;
				}else if($applcation_result->Response->Applications[0]->Status == "Bank Information Pending"){
					wp_redirect("https://".$_SERVER["HTTP_HOST"]."/los-full-application/bank-linking"); exit;
				}else if($applcation_result->Response->Applications[0]->Status == "Document Upload Pending"){
					wp_redirect("https://".$_SERVER["HTTP_HOST"]."/los-full-application/upload-documents"); exit;
				}else{
					wp_redirect("https://".$_SERVER["HTTP_HOST"]."/application-status"); exit;
				}
			}
		}
	}
	
	if(isset($_POST['mycustom_user_email']) && wp_verify_nonce($_POST['mycustom_forgot_nonce'], 'mycustom-forgot-nonce')) {
		
		// this returns the user ID and other info from the user name
		$user = get_user_by('login',$_POST['mycustom_user_email']."_".$partner_theme_config['slug']);
		//print_r($user);exit;
		if(!$user) {
			// if the user name doesn't exist
			mycustom_errors()->add('empty_username', __('Invalid username'));
		}
 
		// retrieve all error messages
		$errors = mycustom_errors()->get_error_messages();
 
		// only reset the user in if there are no errors
		if(empty($errors)) {
 
			$code = sha1( $user->ID . time() );
			$forgot_link = add_query_arg( array( 'key' => $code, 'user' => $user->ID ), get_permalink( 5448 ));
			update_user_meta( $user->ID, 'has_to_be_activated', $code, true );
			$reset_password = new MandrillHandler();
			$user_data = array();
			$user_data['firstname'] = $user->first_name;
			$user_data['email'] = $_POST['mycustom_user_email'];
			$user_data['forgot_link'] = $forgot_link;
			$response = $reset_password->scheduleForgotPassword($user_data);
 
			// send the newly created user to the home page after logging them in
			//wp_redirect("https://".$_SERVER["HTTP_HOST"]."/los-full-application/app-page-1"); exit;
		}
	}
	
	if(isset($_POST['mycustom_user_email']) && wp_verify_nonce($_POST['mycustom_resend_nonce'], 'mycustom-resend-nonce')) {
		
		// this returns the user ID and other info from the user name
		$user = get_user_by('login',$_POST['mycustom_user_email']."_".$partner_theme_config['slug']);
		//print_r($user);exit;
		if(!$user) {
			// if the user name doesn't exist
			mycustom_errors()->add('empty_username', __('Invalid username'));
		}
 
		// retrieve all error messages
		$errors = mycustom_errors()->get_error_messages();
 
		// only reset the user in if there are no errors
		if(empty($errors)) {
 
			$code = get_user_meta( $user->ID, 'has_to_be_activated', true );
			$activation_link = add_query_arg( array( 'key' => $code, 'user' => $user->ID ), get_permalink( 5445 ));
			$verify_email = new MandrillHandler();
			$user_data = array();
			$user_data['firstname'] = $user->first_name;
			$user_data['email'] = $_POST['mycustom_user_email'];
			$user_data['activation_link'] = $activation_link;
			$response = $verify_email->scheduleEmailVerification($user_data);
 
			// send the newly created user to the home page after logging them in
			//wp_redirect("https://".$_SERVER["HTTP_HOST"]."/los-full-application/app-page-1"); exit;
		}
	}
}
add_action('init', 'mycustom_login_member');

// register a new user
function mycustom_add_new_member() {
	global $isExtended,$configData,$partner_theme_config;
	global $versionConfigData,$bank_slug;
	if(!session_id()){@session_start();}
	
  	if (isset( $_POST["mycustom_user_email"] ) && wp_verify_nonce($_POST['mycustom_register_nonce'], 'mycustom-register-nonce')) {
		$user_login		= $_POST["mycustom_user_email"]."_".$_POST['mycustom_user_bank_slug'];	
		$user_email		= $_POST["mycustom_user_email"];
		$user_fname		= $_POST["mycustom_user_fname"];
		$user_uuid		= $_POST["mycustom_user_uuid"];
		$user_pass		= $_POST["mycustom_user_pass"];
		$pass_confirm 	= $_POST["mycustom_user_pass_confirm"];
 
		// this is required for username checks
		require_once(ABSPATH . WPINC . '/registration.php');
 
		if(username_exists($user_login)) {
			// Username already registered
			mycustom_errors()->add('username_unavailable', __('Username already taken'));
		}
		if(!validate_username($user_login)) {
			// invalid username
			mycustom_errors()->add('username_invalid', __('Invalid username'));
		}
		if($user_login == '') {
			// empty username
			mycustom_errors()->add('username_empty', __('Please enter a username'));
		}
		/*if(!is_email($user_email)) {
			//invalid email
			mycustom_errors()->add('email_invalid', __('Invalid email'));
		}*/
		
		if($user_pass == '') {
			// passwords do not match
			mycustom_errors()->add('password_empty', __('Please enter a password'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			mycustom_errors()->add('password_mismatch', __('Passwords do not match'));
		}
 
		$errors = mycustom_errors()->get_error_messages();
		
		// only create the user in if there are no errors
		if(empty($errors)) {
 
			$new_user_id = wp_insert_user(array(
					'user_login'		=> $user_login,
					'user_pass'	 		=> $user_pass,
					'user_email'		=> $user_login,
					'first_name'		=> $user_fname,
					'description'		=> $user_uuid,
					'user_registered'	=> date('Y-m-d H:i:s'),
					'role'				=> 'subscriber'
				)
			);
			//print_r($new_user_id);exit('call123');
			if($new_user_id && !is_wp_error( $new_user_id )) {
				$isExtended = '1';
				$borrower_curl = New Curl();
				$dvConfigData = (object)$configData['dv360'];
				//$url=$dvConfigData->post_url;
				//$url= "https://" . $_SERVER['HTTP_HOST'] . $dvConfigData->post_url;
				$url= "https://" . $_SERVER['HTTP_HOST'] . "/DV360MockNew.php";
				$data['TransactionType'] = "getBorrowerInfo";
				$result = $borrower_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
				$borrower_json = json_encode($result->Response);
				$_SESSION['extendedAppData'] = $borrower_json;
				
				$applcation_curl = New Curl();
				$dvConfigData = (object)$configData['dv360'];
				//$url=$dvConfigData->post_url;
				//$url= "https://" . $_SERVER['HTTP_HOST'] . $dvConfigData->post_url;
				$data['TransactionType'] = "LOSBorrowerStatus";
				$applcation_result = $applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
				$applcation_json = json_encode($applcation_result->Response);
				//echo count($applcation_result->Response->Applications);exit('call');
				$_SESSION['application_data'] = $applcation_json;
				
				// send an email to the admin alerting them of the registration
				//wp_new_user_notification($new_user_id);
				
				$code = sha1( $new_user_id . time() );
				$activation_link = add_query_arg( array( 'key' => $code, 'user' => $new_user_id ), get_permalink( 5445 ));
				add_user_meta( $new_user_id, 'has_to_be_activated', $code, true );
				
				// Send verification email via Mandrill
				$verify_email = new MandrillHandler();
				$user_data = array();
				$user_data['firstname'] = $user_fname;
				$user_data['email'] = $user_email;
				$user_data['activation_link'] = $activation_link;
				$response = $verify_email->scheduleEmailVerification($user_data);
				//print_r($response);exit('call4');
				// log the new user in
				wp_setcookie($user_login, $user_pass, true);
				wp_set_current_user($new_user_id, $user_login);	
				do_action('wp_login', $user_login);
 
				// send the newly created user to the home page after logging them in
				if(isset($partner_theme_config['slug']) && $partner_theme_config['slug'] == 'gdsuk' || isset($partner_theme_config['slug']) && $partner_theme_config['slug'] == 'gdsukht' || isset($partner_theme_config['slug']) && $partner_theme_config['slug'] == 'gdsukhd'){
					wp_redirect("https://".$_SERVER["HTTP_HOST"]."/uk-registration-successful");
				}else{
					if(strpos($_SERVER['REQUEST_URI'],'5point') !== false){
						wp_redirect("https://".$_SERVER["HTTP_HOST"]."/5point/registration-successful");
					}else{
						wp_redirect("https://".$_SERVER["HTTP_HOST"]."/registration-successful");
					}
				}
				//wp_redirect($activation_link);		
				exit;
			}
 
		}
 
	}
}
add_action('init', 'mycustom_add_new_member');

// Reset password after submitting a form
function mycustom_reset_passwordpage() {
	global $partner_theme_config,$configData;
	if(!session_id()){@session_start();}
		
	if(isset($_POST['mycustom_user_id']) && isset($_POST['mycustom_user_code']) && wp_verify_nonce($_POST['mycustom_reset_nonce'], 'mycustom-reset-nonce')) {
		$user_pass		= $_POST["mycustom_user_pass"];
		$pass_confirm 	= $_POST["mycustom_user_pass_confirm"];
		// this returns the user info from the user ID
		$user = get_user_by('id',$_POST['mycustom_user_id']);
		//print_r($user);exit;
		if(!$user) {
			// if the user name doesn't exist
			mycustom_errors()->add('empty_username', __('Invalid username'));
		}
		
		if($user_pass == '') {
			// passwords do not match
			mycustom_errors()->add('password_empty', __('Please enter a password'));
		}
		if($user_pass != $pass_confirm) {
			// passwords do not match
			mycustom_errors()->add('password_mismatch', __('Passwords do not match'));
		}
 
		// retrieve all error messages
		$errors = mycustom_errors()->get_error_messages();
 
		// only reset the password if there are no errors
		if(empty($errors)) {
			delete_user_meta( $user->ID, 'has_to_be_activated' );
			reset_password($user,$user_pass);
			// send the user to the extended app page after resetting password
			wp_redirect("https://".$_SERVER["HTTP_HOST"]."/borrower-login"); exit;
		}
	}
}
add_action('init', 'mycustom_reset_passwordpage');

function mycustom_pages_load_css(){
	global $partner_theme_config;
	if(isset($partner_theme_config)){
		wp_enqueue_style($partner_theme_config['slug'].'_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/'.$partner_theme_config['breadcrumb_css']);
		wp_enqueue_style($partner_theme_config['slug'].'_breadcrumb_transition_css',get_stylesheet_directory_uri().'/css/'.$partner_theme_config['breadcrumb_transition_css']);
	}
}
if(strpos($_SERVER['REQUEST_URI'],'tila-thankyou') !== false || strpos($_SERVER['REQUEST_URI'],'document-upload-demo') !== false || strpos($_SERVER['REQUEST_URI'],'auto-loan-extended/thankyou') !== false || strpos($_SERVER['REQUEST_URI'],'auto-loan-extended/tila-document') !== false || strpos($_SERVER['REQUEST_URI'],'auto-loan-extended/bank-linking') !== false || strpos($_SERVER['REQUEST_URI'],'payday-loan/upload-documents') !== false || strpos($_SERVER['REQUEST_URI'],'payday-loan/tila-document') !== false || strpos($_SERVER['REQUEST_URI'],'payday-loan/bank-linking') !== false || strpos($_SERVER['REQUEST_URI'],'uk-application/tila-document') !== false || strpos($_SERVER['REQUEST_URI'],'uk-application/phonenumber') !== false || strpos($_SERVER['REQUEST_URI'],'uk-application/soft-enquiry') !== false || strpos($_SERVER['REQUEST_URI'],'uk-application/hard-enquiry') !== false || strpos($_SERVER['REQUEST_URI'],'small-business/sme-borrower-thankyou') !== false || strpos($_SERVER['REQUEST_URI'],'small-business/sme-co-owner-thankyou') !== false || strpos($_SERVER['REQUEST_URI'],'small-business/sme-thankyou') !== false || strpos($_SERVER['REQUEST_URI'],'small-business/sme-document') !== false || strpos($_SERVER['REQUEST_URI'],'small-business/sme-application-status') !== false || strpos($_SERVER['REQUEST_URI'],'small-business/sme-coborrower-status') !== false || strpos($_SERVER['REQUEST_URI'],'small-business/upload-documents') !== false || strpos($_SERVER['REQUEST_URI'],'small-business/bank-linking') !== false || strpos($_SERVER['REQUEST_URI'],'uk-application/bank-linking') !== false || strpos($_SERVER['REQUEST_URI'],'bank-transaction-details') !== false || strpos($_SERVER['REQUEST_URI'],'bank-transaction/thankyou') !== false || strpos($_SERVER['REQUEST_URI'],'bank-transaction') !== false || $_SERVER["HTTP_HOST"] == 'demos.api-dataview360.com' || strpos($_SERVER['REQUEST_URI'],'los-access-offers') !== false || strpos($_SERVER['REQUEST_URI'],'ppp-application/thank-you') !== false || strpos($_SERVER['REQUEST_URI'],'ppp-application/ppp-document') !== false || strpos($_SERVER['REQUEST_URI'],'ppp-application/complete') !== false || strpos($_SERVER['REQUEST_URI'],'ppp-application/additional-required-documents') !== false || strpos($_SERVER['REQUEST_URI'],'ppp-application/required-document-upload') !== false || strpos($_SERVER['REQUEST_URI'],'los-full-application/bank-linking') !== false || strpos($_SERVER['REQUEST_URI'],'ppp-application/bank-linking') !== false || strpos($_SERVER['REQUEST_URI'],'los-full-application/upload-documents') !== false || strpos($_SERVER['REQUEST_URI'],'create-borrower') !== false || strpos($_SERVER['REQUEST_URI'],'borrower-login') !== false || strpos($_SERVER['REQUEST_URI'],'reset-password') !== false || strpos($_SERVER['REQUEST_URI'],'los-full-application/thank-you') !== false || strpos($_SERVER['REQUEST_URI'],'los-full-application/tila-thank-you') !== false || strpos($_SERVER['REQUEST_URI'],'los-full-application/tila-document') !== false || strpos($_SERVER['REQUEST_URI'],'email-verification') !== false || strpos($_SERVER['REQUEST_URI'],'registration-successful') !== false || strpos($_SERVER['REQUEST_URI'],'application-status') !== false){ 
	add_action('init', 'mycustom_pages_load_css');
}



// used for tracking error messages
function mycustom_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function mycustom_show_error_messages() {
	if($codes = mycustom_errors()->get_error_codes()) {
		echo '<div class="mycustom_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = mycustom_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}

// register our form css
function mycustom_register_css() {
	wp_register_style('mycustom-form-css', plugin_dir_url( __FILE__ ) . '/forms.css');
	if(isset($partner_theme_config) && !empty($partner_theme_config)){
		wp_enqueue_style($partner_theme_config['slug'].'_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/'.$partner_theme_config['breadcrumb_css']);
		wp_enqueue_style($partner_theme_config['slug'].'_breadcrumb_transition_css',get_stylesheet_directory_uri().'/css/'.$partner_theme_config['breadcrumb_transition_css']);
	}
}
add_action('wp_footer', 'mycustom_register_css');

// load our form css
function mycustom_print_css() {
	global $mycustom_load_css;
 
	// this variable is set to TRUE if the short code is used on a page/post
	if ( ! $mycustom_load_css )
		return; // this means that neither short code is present, so we get out of here
 
	wp_print_styles('mycustom-form-css');
}
add_action('wp_footer', 'mycustom_print_css');

add_action( 'template_redirect', 'mycustom_activate_user' );
function mycustom_activate_user() {
	global $partner_theme_config,$configData;
    if ( is_page() && get_the_ID() == 5445 ) {
        $user_id = filter_input( INPUT_GET, 'user', FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
        if ( $user_id ) {
            // get user meta activation hash field
            $code = get_user_meta( $user_id, 'has_to_be_activated', true );
            if ( $code == filter_input( INPUT_GET, 'key' ) ) {
                delete_user_meta( $user_id, 'has_to_be_activated' );
			}
				if(isset($_GET['application_type']) && $_GET['application_type'] == 'SME'){
					$borrower_curl = New Curl();
					$dvConfigData = (object)$configData['dv360'];
					$url=$dvConfigData->mock_url;
					$data['TransactionType'] = "getBorrowerInfo";
					$result = $borrower_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
					$borrower_json = json_encode($result->Response);
					$_SESSION['extendedAppData'] = $borrower_json;
					
					$sme_applcation_curl = New Curl();
					$dvConfigData = (object)$configData['dv360'];
					$url=$dvConfigData->mock_url;
					$data['TransactionType'] = "smeCheckoffDocuments";
					$applcation_result = $sme_applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
					$applcation_json = json_encode($applcation_result->Response); 
					//echo $applcation_json;exit('call');
					$_SESSION['sme_document_data'] = $applcation_json;
					//setcookie('sme_document_data',$applcation_json,0,'/','api-dataview360.com');
					$applcation_curl = New Curl();
					$dvConfigData = (object)$configData['dv360'];
					$url=$dvConfigData->mock_url;
					$smeData = array();
					if(strpos($_SERVER["HTTP_HOST"],"demo-bankofcolorado.api-dataview360.com") !== false){
						$smeData['TransactionType'] = 'retrieveOffersBOC';
					}elseif(isset($_GET['isIncredible']) && $_GET['isIncredible'] == 'true'){
						$smeData['TransactionType'] = 'SMEIncredibleOffers';
					}else{
						$smeData['TransactionType'] = 'retrieveOffers';
					}
					$smeData['loanAmount'] = $_GET['loanAmount'];
					$_SESSION['loanAmount'] = $_GET['loanAmount'];
					$sme_applcation_result = $applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$smeData));
					$_SESSION['isOfferAvailable'] = 1;
					$_SESSION[$session_id] = $sme_applcation_result;
					$_SESSION[session_id().'-formData'] = $smeData;
					$_SESSION['offerAvailableArray'] = array_merge($_SESSION['offerAvailableArray'],array(session_id()));
					if(isset($_GET['isIncredible']) && $_GET['isIncredible'] == 'true'){
						wp_redirect("https://".$_SERVER["HTTP_HOST"]."/small-business/offers");exit;
					}else{
						wp_redirect("https://".$_SERVER["HTTP_HOST"]."/personal-loans/pre-qualify/offers");exit;
					}
					//print_r($sme_applcation_result);exit('call');
					//makeRequestToDV360('pre-qualify',$smeData);exit;
				}
            
        }
    }
	
	if ( is_page() && get_the_ID() == 5734 && !isset($_SESSION['emailSent']) ) {
		$sme_data['owners'] = json_decode(stripslashes($_SESSION['sme_application_data']['owners']),true);
		if(isset($_SESSION['user_id']) && isset($sme_data['owners'][0]['ownerEmail'])){
			if(strpos($_SERVER['HTTP_HOST'],'demo-incredible.api-dataview360.com') !== false){
				//$activation_link = add_query_arg( array( 'key' => $_SESSION['user_code'], 'user' => $_SESSION['user_id'], 'application_type' => 'SME', 'loanAmount' => $_SESSION['sme_application_data']['loanAmount'], 'isIncredible' => 'true' ), get_permalink( 5445 ));
				$activation_link = "https://".$_SERVER['HTTP_HOST']."/email-verification?key=".$_SESSION['user_code']."&user=".$_SESSION['user_id']."&application_type=SME&loanAmount=".$_SESSION['sme_application_data']['loanAmount']."&isIncredible=true";
			}else{
				$activation_link = add_query_arg( array( 'key' => $_SESSION['user_code'], 'user' => $_SESSION['user_id'], 'application_type' => 'SME', 'loanAmount' => $_SESSION['sme_application_data']['loanAmount'] ), get_permalink( 5445 ));
			}
			
			$_SESSION['sme_activation_link'] = $activation_link;
			// Send verification email via Mandrill
			$verify_email = new MandrillHandler();
			$user_data = array();
			$user_data['firstname'] = $sme_data['owners'][0]['ownerFirstName'];
			$user_data['email'] = $sme_data['owners'][0]['ownerEmail'];
			$user_data['lenderName'] = $partner_theme_config['theme_name'];
			$user_data['activation_link'] = $activation_link;
			$response = $verify_email->scheduleEmailVerificationSME($user_data);
		}
		if(isset($sme_data['owners'][1]['ownerEmail'])){
			$objMandrill = New MandrillHandler();
			$mandrill_postData['firstname'] = $sme_data['owners'][1]['ownerFirstName'];
			$mandrill_postData['businessName'] = ($_SESSION['sme_application_data']['businessLegalName'])?$_SESSION['sme_application_data']['businessLegalName']:'';
			$mandrill_postData['email'] = $sme_data['owners'][1]['ownerEmail'];
			if(isset($_SESSION['SME_applicationID'])){
				$mandrill_postData['accessURL'] = "https://".$_SERVER['HTTP_HOST']."/small-business/app-page-1?coBorrowerRequired=1&email=".$mandrill_postData['email']."&applicationID=".$_SESSION['SME_applicationID'];
			}else{
				$mandrill_postData['accessURL'] = "https://".$_SERVER['HTTP_HOST']."/small-business/app-page-1?coBorrowerRequired=1&email=".$mandrill_postData['email'];
			}
			$mandrill_postData['lenderName'] = $partner_theme_config['theme_name'];
			$objMandrill->scheduleCoOwnerEmail($mandrill_postData);
		}
		$_SESSION['emailSent'] = 'true';
	}
}


// Custom switch theme function for headway domain
function custom_theme_switch_gds(){
	global $versionConfigData,$configData;
	global $partner_theme,$partner_theme_config;
	//echo '<script>alert("In session script123");</script>';
	if(!session_id()){@session_start();}
	
	//print_r($versionConfigData);exit('456');
	if(isset($versionConfigData['domains']) && !empty($versionConfigData['domains'])){
		foreach($versionConfigData['domains'] as $host){
			if(isset($host['hosts']) && is_array($host['hosts'])){
				foreach($host['hosts'] as $host_url){
					if($_SERVER['HTTP_HOST'] == $host_url['host']){
						if(isset($_GET['coBorrowerRequired']) && $_GET['coBorrowerRequired'] == "1"){
							$_SESSION['isSMECoborrower'] = 'true';
							$borrower_curl = New Curl();
							$dvConfigData = (object)$configData['dv360'];
							$url=$dvConfigData->mock_url;
							$data['TransactionType'] = "getBorrowerInfo";
							$result = $borrower_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
							$borrower_json = json_encode($result->Response);
							$_SESSION['extendedAppData'] = $borrower_json;
				
							$sme_coborrower_applcation_curl = New Curl();
							$dvConfigData = (object)$configData['dv360'];
							$url=$dvConfigData->mock_url;
							$data['TransactionType'] = "smeCheckoffCoborrowerDocuments";
							$coborrower_applcation_result = $sme_coborrower_applcation_curl->callApi($url, 'POST','application/json',array('Request'=>$data));
							$coborrower_applcation_json = json_encode($coborrower_applcation_result->Response); 
							$_SESSION['sme_coborrower_document_data'] = $coborrower_applcation_json;
						}
						$partner_theme = $host['theme'];
						if(strpos($_SERVER['REQUEST_URI'],'5point') !== false){
							$partner_theme = 'prime-rates-5point';
						}
						$partner_theme_config = $host;
						add_filter( 'pre_option_stylesheet', 'load_partner_theme', 10 );
						add_filter( 'pre_option_template', function() { return 'flat-responsive-pro'; } );
					}
				}
			}/*else if(strpos($_SERVER['REQUEST_URI'], $host['slug']) !== false){
				$partner_theme = $host['theme'];
				$partner_theme_config = $host;
				add_filter( 'pre_option_stylesheet', 'load_partner_theme', 10 );
				add_filter( 'pre_option_template', function() { return 'flat-responsive-pro'; } );
			}*/
		}
	}
	
}
function load_partner_theme(){
	global $partner_theme;
	return $partner_theme;
}
//custom_theme_switch_gds();
if ( ! is_admin() ) {
	add_action('plugins_loaded','custom_theme_switch_gds');
}