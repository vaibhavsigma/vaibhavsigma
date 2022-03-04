<?php
/**
 * Plugin Name: Prime Rates Loan Applications
 * Plugin URI: http://sigmainfo.net/
 * Description: Customized Request
 * Version: 0.0
 * Author: Sigma Info Solutions
 * Author URI: http://www.sigmainfo.net/
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
define( 'PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRIME_RATES_LOAN_APPLICATIONS_STATIC_DIR', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_DIR.'static'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_LOAN_APPLICATIONS_JS_DIR', PRIME_RATES_LOAN_APPLICATIONS_STATIC_DIR.'js'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_LOAN_APPLICATIONS_JSON_DIR', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_DIR.'json'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_LOAN_APPLICATIONS_CLASS_DIR', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_DIR.'class'.DIRECTORY_SEPARATOR );

define( 'PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL', plugins_url().DIRECTORY_SEPARATOR.'prime-rates-loan-applications'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_LOAN_APPLICATIONS_STATIC_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_LOAN_APPLICATIONS_JS_URL', PRIME_RATES_LOAN_APPLICATIONS_STATIC_URL.'js'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_LOAN_APPLICATIONS_CSS_URL', PRIME_RATES_LOAN_APPLICATIONS_STATIC_URL.'css'.DIRECTORY_SEPARATOR);
require_once(PRIME_RATES_LOAN_APPLICATIONS_CLASS_DIR.'curl.php');
require_once(PRIME_RATES_LOAN_APPLICATIONS_CLASS_DIR.'mailChimp.php');
require_once(PRIME_RATES_LOAN_APPLICATIONS_CLASS_DIR.'mandrillHandler.php');

global $post;
global $wpdb;
global $tableName;
global $configData;
global $plLenderConfig;
global $PLconfigData;

$tableName = $wpdb->prefix . 'custom_api_logs';
$configData = readJson(PRIME_RATES_LOAN_APPLICATIONS_JSON_DIR.'config.json');
$PLconfigData = readJson(PRIME_RATES_LOAN_APPLICATIONS_JSON_DIR.'PL_LA_Configuration.json');

if(strpos($_SERVER["HTTP_HOST"],"dev.primerates.com") !== false 
|| strpos($_SERVER["HTTP_HOST"],"dev.apply.headwaysales.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"dev.pr.acornfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"dev-kn.pr.acornfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"dev.apply.retainfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"devloans.zalea.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-dev.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"dev-bankofcolorado.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"dev-incredible.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-dev-uk.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-dev-uk-ht.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-dev-woodforest.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-dev-abcbank.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-dev-tim.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"dev-uk-hd.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"ppp-dev.api-dataview360.com") !== false){
	$configData = $configData["dev"];
	// echo "<pre>";print_r($configData);echo "</pre>";exit;
}else if(strpos($_SERVER["HTTP_HOST"],"qa.primerates.com") !== false 
|| strpos($_SERVER["HTTP_HOST"],"qa.apply.headwaysales.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa.pr.acornfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa-kn.pr.acornfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa.apply.retainfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qaloans.zalea.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-qa.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-demo.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa.woodforest.primerates.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa.abcbank.primerates.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"ppp-demo.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-qa-uk.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa-uk-moneyfacts.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo-sterlingplanet.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa-bankofcolorado.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo-bankofcolorado.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa-incredible.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo-incredible.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo-cashmax.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo-ilending.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-qa-uk.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-demo-uk.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-qa-uk-ht.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-demo-uk-ht.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa-uk-hd.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo-uk-hd.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-qa-woodforest.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-qa-abcbank.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-demo-woodforest.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-qa-tim.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-demo-tim.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"los-demo-abcbank.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo-uk-moneyfacts.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"bt-demo.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"ppp-qa.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"qa-coopertiva.api-dataview360.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"demo-coopertiva.api-dataview360.com") !== false){
	$configData = $configData["qa"];
}else if(strpos($_SERVER["HTTP_HOST"],"uat.primerates.com") !== false 
|| strpos($_SERVER["HTTP_HOST"],"uat.apply.headwaysales.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"uat.pr.acornfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"uat.pr.acornfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"uat-kn.pr.acornfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"uat.apply.retainfinance.com") !== false
|| strpos($_SERVER["HTTP_HOST"],"uatloans.zalea.com") !== false){
	$configData = $configData["uat"];
}else{
	$configData = $configData["prod"];
}

include_once(plugin_dir_path( __FILE__ ) . 'gds-loan-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-multipage-loan-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-breadcrumb-loan-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-offers-page.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-no-offers-page.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-affiliate-loan-applications.php');
require_once(plugin_dir_path( __FILE__ ) . 'prime-rates-leadcheck-loan-applications.php');

//echo '<pre>';print_r($configData);
// function to create the DB / Options / Defaults
function prime_rates_loan_applications_options_install() {
    global $wpdb;
    global $tableName;

    // create the ECPT metabox database table
    if($wpdb->get_var("show tables like '$tableName'") != $tableName)
    {
        $sql = "CREATE TABLE " . $tableName . " (
		  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `request_url` varchar(255) NOT NULL,
		  `method` varchar(10) NOT NULL DEFAULT 'GET',
		  `content_type` varchar(50) NOT NULL DEFAULT 'Content-Type: text/plain',
		  `request_params` text NULL,
		  `response_params` text NULL,
		  `session_id` text NULL,
		  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'prime_rates_loan_applications_options_install');

function apply_for_loans_shortcode($atts = []){

	session_destroy();
	session_unset();	
	$atts = (array)$atts;
	$variables = "";
	if(isset($atts['step'])){
		$stepNo = (int)$atts['step'];
	}else{
		$stepNo =0;
	}
	if($stepNo>1 && !isset($_SESSION['multi_step_data']) && !$_POST){
		$url = get_site_url(). '/personal-loans/app-form-start';
		wp_redirect( $url );
		exit;
	}
	if(isset($atts['multi-step']) && strtolower($atts['multi-step']) == 'yes'){
		$loadMultiStepForm = 1;
	}else{
		$loadMultiStepForm = 0;
	}
	// Check for offer available session variable
    /*if (isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 1){
		if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"zalea.com") !== false){
			$offer_url = '/offers';
		}else{
			$offer_url = get_site_url(). '/personal-loans/pre-qualify/offers';
		}
		wp_redirect( $offer_url );
		exit;
	}else{
		$isOfferAvailable = '0';
	}*/
	$jsString = '<script type="text/javascript">';
	$jsString .= 'window.isMultiStep = '.$loadMultiStepForm.';';
	// Custom URI parameter for checking the headway sales parameter
	if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){
		$jsString .= 'window.isHeadway = "1";';
	}else{
		$jsString .= 'window.isHeadway = "0";';
	}
	if(strpos($_SERVER['REQUEST_URI'],'personal-loans/access-offers') !== false){
		$jsString .= 'window.isAccessOffers = "1";';
	}
	if(isset($_COOKIE['accessOffersCount'])){
		$jsString .= 'window.attemptCount = "'.$_COOKIE['accessOffersCount'].'";';
	}else{
		$jsString .= 'window.attemptCount = "0";';
	}
	if(isset($_SESSION['expired_result']) && $_SESSION['expired_result'] != ""){
		$jsString .= 'window.lenderResponse = '.$_SESSION['expired_result'].';';
	}
	if(isset($_SESSION['dealerid'])){
		$jsString .= 'window.dealerid = "'.$_SESSION['dealerid'].'";';
	}
	//$jsString .= 'window.isOfferAvailable = "'.$isOfferAvailable.'";';
	$jsString .= 'window.isOfferAvailable = "0";';
	
	if($_POST){
		multi_step_form_submit($_POST);
		if(isset($_SESSION['multi_step_data'])){
			$variables = $_SESSION['multi_step_data'];
		}else{
			$variables = $_POST;
		}
	}else if(isset($_SESSION['multi_step_data'])){
			$variables = $_SESSION['multi_step_data'];
	}else if($_GET){
		$variables = $_GET;
	}
	if($variables){
		$loanAmount = isset($variables["loanamount"])?$variables["loanamount"]:"";
		$loanPurpose = isset($variables["loanPurpose"])?$variables["loanPurpose"]:"";
		// Load partner id according to environment
		if(isset($loadMultiStepForm) && $loadMultiStepForm == 1){
			$partnerId = isset($variables["partnerid"])?$variables["partnerid"]:"";
		}else{
			$partnerId = isset($variables["partnerId"])?$variables["partnerId"]:"";
		}
		if(isset($variables["affiliateid"])){
			$jsString .= 'window.affiliateid = "'.$variables["affiliateid"].'";';
		}
		$featured = isset($variables["featured"])?$variables["featured"]:"";
		$jsString .= 'window.loanAmount = "'.$loanAmount.'";';
		$jsString .= 'window.loanPurpose = "'.$loanPurpose.'";';
		$jsString .= 'window.partnerId = "'.$partnerId.'";';
		$jsString .= 'window.featured = "'.$featured.'";';
		$jsString .= 'window.postData = '.json_encode($variables).';';


	}
		
	$jsString .='window.stepNo='.$stepNo.';</script>';
	
	echo $jsString;
	if(strpos($_SERVER['REQUEST_URI'],'access-offers') === false){
		if(strpos($_SERVER["HTTP_HOST"],"primerates.com") !== false){	
		?>
			<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/SinglePage/singlepage-primerate.css" rel="stylesheet">
			<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/transition-primerate.css" rel="stylesheet">
		<?php 
		}
		if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){	
		?>
			<link href="<?php echo 'https://'.$_SERVER["HTTP_HOST"]; ?>/wp-content/themes/prime-rates-head-way-sales/css/SinglePage/singlepage-headway.css" rel="stylesheet">
			<link href="<?php echo 'https://'.$_SERVER["HTTP_HOST"]; ?>/wp-content/themes/prime-rates-head-way-sales/css/transition-headway.css" rel="stylesheet">
		<?php 
		}
	} ?>
	<div class="main-cont" id="root"></div>	
	<?php 
}
function prime_rates_loan_applications_enqueue() {
 global $post,$partner_theme_config;
 $action='';
 if(has_shortcode( $post->post_content, 'apply-for-loans')){
	if(strpos($_SERVER['REQUEST_URI'],'personal-loans/access-offers') === false && strpos($_SERVER['REQUEST_URI'],'access-offers') === false && !isset($_GET['leadCheckID']) && @$_GET['affiliateid'] != 'intercom' && @$_GET['affiliateid'] != '426464' && @$_GET['affiliateid'] != 'CreditSoup' && @$_GET['affiliateid'] != '430380' && @$_GET['affiliateid'] != '434151' && @$_POST['affiliateid'] != 'intercom' && @$_POST['affiliateid'] != '426464' && @$_POST['affiliateid'] != 'CreditSoup' && @$_POST['affiliateid'] != '430380' && @$_POST['affiliateid'] != '434151'){ 
		wp_enqueue_script('prime_rates_loan_applications', 'https://'.$_SERVER["HTTP_HOST"].'/wp-content/plugins/prime-rates-loan-applications/static/js/main.28d70e99.js', array(), 0,false );
	}
	 
	  wp_enqueue_script('prime_rates_loan_applications_google', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyB_bGYpM1GXZaS-a3kivfDQXoP038sQmW8&libraries=places&expostbox=true', array(), 0,false );
	  if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){
		  $action = 'headway_mailchimp';
	  }else if(strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
		  $action = 'retain_mailchimp';
	  }else if(strpos($_SERVER["HTTP_HOST"],"zalea.com") !== false){
		  $action = 'mandrill_incomplete';
	  }else{
		  $action = 'mandrill_complete';
	  }
 }
	if($action){
		 wp_localize_script('prime_rates_loan_applications', 'ajax_var', array(
			 'url' => admin_url('admin-ajax.php'),
			 'nonce' => wp_create_nonce($action)
			));
	}
}
function prime_rates_loan_applications_shortcodes_init(){
	  //add_shortcode('pre-qualified-offers', 'pre_qualified_offers_shortcode');
	  add_shortcode('apply-for-loans', 'apply_for_loans_shortcode');
}

function ajax_login_init(){
  if ( ! is_user_logged_in()) {
    return;
  }
}
function mandrill_incomplete()
{
	// echo "<pre>";print_r($_POST);
  $nonce = $_POST['nonce'];
 // Verify nonce field passed from javascript code
    //if ( ! wp_verify_nonce( $nonce, '4109b2257c' ) )
        //die ( 'Busted!');
	if($_POST){
		$objMandrill = New MandrillHandler();
		cancelScheduledEmail($objMandrill);
		if(isset($_SESSION['multi_step_data'])){
			$variables = $_SESSION['multi_step_data'];
		}else{
			$variables = $_POST;
		}
		$response = $objMandrill->scheduleAbandonedEmail($_POST);
		$_SESSION['incomplete_mandrill_response'] = $response;
		//cancelScheduledEmail($objMandrill);
	}
}
function mandrill_complete(){
	$objMandrill = New MandrillHandler();
	cancelScheduledEmail($objMandrill);
	if(isset($_SESSION['multi_step_data'])){
		$variables = $_SESSION['multi_step_data'];
	}else{
		$variables = $_POST;
	}
	$objMandrill->scheduleThankYouEmail($_POST);
}

function primerates_mailchimp($postData){
	//print_r($postData);exit;
	$objMandrill = New MandrillHandler();
	cancelScheduledEmail($objMandrill);
	if(isset($postData['offers']) && $postData['offers'] == 'true'){
		$objMandrill->scheduleThankYouEmail($postData);
	}else{
		$objMandrill->scheduleNoOffersEmail($postData);
	}
}

function headway_mailchimp($postData){
	global $configData;
	$objMandrill = New MandrillHandler();
	cancelScheduledEmail($objMandrill);
	if(isset($postData['is_offer']) && $postData['is_offer'] == 'yes'){
		$objMandrill->scheduleThankYouEmailHeadway($postData);
	}else{
		$objMandrill->scheduleNoOffersEmailHeadway($postData);
	}
	//$objMailChimp = NEW MailChimp();
	//$objMailChimp->listId = $configData['system']['mail-chimp']['headway-newsletter-list-id']; // Headway Newsletter - Development List ID
	//$response = $objMailChimp->saveMemberToList($postData);
}

function retain_mailchimp($postData){
	global $configData;
	$objMandrill = New MandrillHandler();
	cancelScheduledEmail($objMandrill);
	if(isset($postData['is_offer']) && $postData['is_offer'] == 'yes'){
		$objMandrill->scheduleThankYouEmailHeadway($postData);
	}else{
		$objMandrill->scheduleNoOffersEmailHeadway($postData);
	}
}

function partner_mailchimp($postData){
	global $configData;
	$objMandrill = New MandrillHandler();
	cancelScheduledEmail($objMandrill);
	if(isset($postData['is_offer']) && $postData['is_offer'] == 'yes'){
		if(isset($postData['physicianEmail']) && $postData['physicianEmail'] != ''){
			$objMandrill->scheduleThankYouEmailPhysician($postData);
		}
		$objMandrill->scheduleThankYouEmailPartner($postData);
	}else{
		if(isset($postData['physicianEmail']) && $postData['physicianEmail'] != ''){
			$objMandrill->scheduleNoOffersEmailPhysician($postData);
		}
		$objMandrill->scheduleNoOffersEmailPartner($postData);
	}
	
}

function log_message(){
	global $configData;
	$log_msg = New Curl();
	// Check for sendEmail flag and pass the parameter accordingly to message log function
	if(isset($_POST['sendEmail']) && $_POST['sendEmail'] == true){
		$log_msg->logMessages($_POST['logMessage'],true);
	}else{
		$log_msg->logMessages($_POST['logMessage']);
	}	
}

function phone_validation_api_error(){
	$objMandrill = New MandrillHandler();
	$data['subject'] = 'NumVerify Error!';
	$data['phoneNumber'] = $_POST['phoneNumber'];
	$data['errormsg'] = $_POST['errormsg'];
	$objMandrill->sendErrorEmail('Mandrill: Admin error email','POST','application/json', $data);
}

function log_partner_message($postData){
	global $configData;
	$log_msg = New Curl();
	// Check for sendEmail flag and pass the parameter accordingly to message log function
	if(isset($postData['sendEmail']) && $postData['sendEmail'] == true){
		$log_msg->logMessages($postData['logMessage'],true);
	}else{
		$log_msg->logMessages($postData['logMessage']);
	}	
}

function acorn_select_offer(){
	//print_r($_POST);exit;
	if(!empty($_POST)) {
		$variables = (object) $_POST;
		global $configData;
		$dvConfigData = (object) $configData['dv360'];
		$curl         = new Curl();
		$url          = $dvConfigData->post_url;
		$method       = 'POST';
		$variables->email = $_SESSION['borrower_email'];
		$variables->firstname = $_SESSION['borrower_firstname'];
		$dvPostData = [
			'TransactionType' => 'Offers',
			'OfferID'         => $variables->offerId,
			'uuid'            => $variables->uuid,
			'ApplicationID'   => $variables->ApplicationID,
			'hsessionid'      => $variables->hsessionid,
			'ReusableOffer'   => $variables->ReusableOffer,
		];
		if ( strpos( $_SERVER["HTTP_HOST"], "primerates.com" ) !== false || strpos( $_SERVER["HTTP_HOST"], "headwaysales.com" ) !== false || strpos( $_SERVER["HTTP_HOST"], "pr.acornfinance.com" ) !== false ) {
			// Mailchimp Complete App List User Offer selected date update on claim offer button click
			$objMailChimp = NEW MailChimp();
			if ( strpos( $_SERVER["HTTP_HOST"], "dev.primerates.com" ) !== false ) {
				$objMailChimp->listId = '5da6879de5';
			} else if ( strpos( $_SERVER["HTTP_HOST"], "qa.primerates.com" ) !== false ) {
				$objMailChimp->listId = '3c5277e1e7';
			} else if ( strpos( $_SERVER["HTTP_HOST"], "www.primerates.com" ) !== false ) {
				$objMailChimp->listId = 'd3b0eba9e8';
			} else if ( strpos( $_SERVER["HTTP_HOST"], "dev.apply.headwaysales.com" ) !== false || strpos( $_SERVER["HTTP_HOST"], "dev.pr.acornfinance.com" ) !== false ) {
				$objMailChimp->listId = 'a3b4255f17';
			} else if ( strpos( $_SERVER["HTTP_HOST"], "qa.apply.headwaysales.com" ) !== false || strpos( $_SERVER["HTTP_HOST"], "qa.pr.acornfinance.com" ) !== false ) {
				$objMailChimp->listId = 'f294365c03';
			} else if ( strpos( $_SERVER["HTTP_HOST"], "apply.headwaysales.com" ) !== false || strpos( $_SERVER["HTTP_HOST"], "pr.acornfinance.com" ) !== false ) {
				$objMailChimp->listId = 'dc8730ca17';
			}
			$data['email']               = $variables->email;
			$data['loanamount']          = $variables->loanamount;
			$data['lender_name']         = $variables->lender_name;
			$data['offer_selected_date'] = date( 'm/d/Y' );
			$mailchimp_response          = $objMailChimp->updateMemberToList( $data );
		}

		$result = $curl->callApi( $url, $method, 'application/json', array( 'Request' => $dvPostData ) );
		if ( $result->http_code != 200 ) {
			$cnt = 1;
			do {
				$result = $curl->callApi( $url, $method, 'application/json', array( 'Request' => $dvPostData ) );
				$cnt ++;
			} while ( $result->http_code == 200 || $cnt == $dvConfigData->attempt_cnt_on_error );
		}
		$result->firstname  = isset( $variables->firstname ) ? $variables->firstname : '';
		$result->loanAmount = isset( $variables->loanamount ) ? $variables->loanamount : '';

		// Added Headway API logging for Offer Selected as per PM-1842
		$headwayConfigData                          = (object) $configData['headway_config'];
		$headway_url                                = $headwayConfigData->post_offer_url;
		$head_curl                                  = new Curl();
		$result_data                                = json_decode( json_encode( $result->Response ), true );
		$headway_data                               = array();
		$headway_data['Request']                    = $_POST;
		$headway_data['Request']['TransactionType'] = 'Offers';
		$headway_data['Request']['OfferID']         = $_POST['offerId'];
		$headway_data['Response']                   = $result_data;
		$method                                     = 'POST';
		$headway_result                             = $head_curl->callApi( $headway_url, $method, 'application/json', $headway_data );
		?>
		<script type="text/javascript">
			localStorage.setItem("lenderResponse", '<?php echo json_encode( $result ); ?>');
		</script>
		<?php
	}
}

add_action('init', 'prime_rates_loan_applications_shortcodes_init');
if(strpos($_SERVER["HTTP_HOST"],"primerates.com") !== false || strpos($_SERVER["HTTP_HOST"],"zalea.com") !== false){
	// Added addition check filter as per PM-706 ticket 03/08/2018
	if($configData['system']['mandrill']['sendToMandrill']==true){
		add_action( 'wp_ajax_mandrill_complete', 'mandrill_complete' );
		add_action( 'wp_ajax_nopriv_mandrill_complete', 'mandrill_complete' );
		add_action( 'wp_ajax_primerates_mailchimp', 'primerates_mailchimp' );
		add_action( 'wp_ajax_nopriv_primerates_mailchimp', 'primerates_mailchimp' );
	}
}

add_action( 'wp_ajax_phone_validation_api_error', 'phone_validation_api_error' );
add_action( 'wp_ajax_nopriv_phone_validation_api_error', 'phone_validation_api_error' );
add_action( 'wp_ajax_acorn_select_offer', 'acorn_select_offer' );
add_action( 'wp_ajax_nopriv_acorn_select_offer', 'acorn_select_offer' );
add_action( 'wp_ajax_mandrill_incomplete', 'mandrill_incomplete' );
add_action( 'wp_ajax_nopriv_mandrill_incomplete', 'mandrill_incomplete' );
add_action( 'wp_ajax_gds_los_mandrill_complete', 'gds_los_mandrill_complete' );
add_action( 'wp_ajax_nopriv_gds_los_mandrill_complete', 'gds_los_mandrill_complete' );
add_action( 'wp_ajax_create_borrowers', 'create_borrowers' );
add_action( 'wp_ajax_nopriv_create_borrowers', 'create_borrowers' );		
add_action( 'wp_ajax_log_message', 'log_message' );
add_action( 'wp_ajax_nopriv_log_message', 'log_message' );
add_action( 'wp_ajax_headway_mailchimp', 'headway_mailchimp' );
add_action( 'wp_ajax_nopriv_headway_mailchimp', 'headway_mailchimp' );
add_action( 'wp_ajax_retain_mailchimp', 'retain_mailchimp' );
add_action( 'wp_ajax_nopriv_retain_mailchimp', 'retain_mailchimp' );
add_action( 'wp_ajax_partner_mailchimp', 'partner_mailchimp' );
add_action( 'wp_ajax_nopriv_partner_mailchimp', 'partner_mailchimp' );
add_action( 'wp_ajax_multi_step_form_submit', 'multi_step_form_submit' );
add_action( 'wp_ajax_nopriv_multi_step_form_submit', 'multi_step_form_submit' );
add_action( 'wp_enqueue_scripts', 'prime_rates_loan_applications_enqueue' );
add_action( 'wp_enqueue_scripts','ajax_login_init' );
function cancelScheduledEmail($objMandrill){
	//echo "<pre>";print_r($_POST);
	if(isset($_SESSION['incomplete_mandrill_response'])){
				$incompleteReponse = $_SESSION['incomplete_mandrill_response'];
				$cancelledResponse = $objMandrill->cancelScheduledEmail($incompleteReponse);
				unset($_SESSION['incomplete_mandrill_response']);
	}
}
function register_session(){
    if( !session_id() )
        @session_start();
	
	if(strpos($_SERVER['HTTP_HOST'],'apply.headwaysales.com') !== false || strpos($_SERVER['HTTP_HOST'],'pr.acornfinance.com') !== false){
		//Expire the session if user is inactive for 2 minutes or more for Headway domain.
		$expireAfter = 30;
	}else{
		//Expire the session if user is inactive for 10 minutes or more.
		$expireAfter = 10;
	}
	
		//Check to see if our "last action" session
		//variable has been set.
		if(isset($_SESSION['last_action'])){
			
			//Figure out how many seconds have passed
			//since the user was last active.
			$secondsInactive = time() - $_SESSION['last_action'];
			
			//Convert our minutes into seconds.
			$expireAfterSeconds = $expireAfter * 60;
			
			//Check to see if they have been inactive for too long.
			if($secondsInactive >= $expireAfterSeconds){
				//User has been inactive for too long.
				//Kill their session.
				session_unset();
				session_destroy();
			}
			
		}
		 
		//Assign the current timestamp as the user's
		//latest activity
		$_SESSION['last_action'] = time();
}
add_action('init','register_session');
function readJson($file){
    $json = file_get_contents($file);
    $jsonData = json_decode($json,true);
    if($jsonData){
        return $jsonData;
    }else{
        ['error'=>"Couldn't parse json"];
    }
}

function destroy_sessions() {
   session_unset();
   session_destroy();
   wp_clear_auth_cookie();//clears cookies regarding WP Auth
}
add_action('wp_logout', 'destroy_sessions');

// Custom switch theme function for headway domain
function custom_theme_switch(){
	global $configData,$partner_theme,$partner_theme_config;
	//echo '<script>alert("In session script123");</script>';
	if(!session_id()){@session_start();}

	// Added PrimeRates personal loan redirect logic as it is not accepting personal loans any more
	if(strpos($_SERVER['HTTP_HOST'],'primerates.com') !== false && strpos($_SERVER['REQUEST_URI'],'personal-loans/app-page-1') !== false){
		wp_redirect( '/thank-you-2' );exit;
	}
	
	if(strpos($_SERVER['HTTP_HOST'],'loans.zalea.com') !== false ){
		// Remove offer session if below given query parameter is passed
		if(isset($_GET['pos']) && $_GET['pos'] == '1'){
			//Kill the session.
			@session_unset();
			@session_destroy();
		}
		add_filter( 'pre_option_stylesheet', function() { return 'prime-rates-zalea'; } );
		add_filter( 'pre_option_template', function() { return 'flat-responsive-pro'; } );
		if((isset($_GET['applicationid']) && $_GET['applicationid'] != '') && (!isset($_GET['physicianEmail']) && $_GET['physicianEmail'] == '')){
			echo '<script>alert("Incorrect URL, Please reach out to your dealer or contractor for the correct URL."); window.location.href = "https://zalea.com";</script>';exit;
		}
		
		if(isset($_GET['physicianEmail']) && $_GET['physicianEmail'] != ''){
			if(!filter_var($_GET['physicianEmail'], FILTER_VALIDATE_EMAIL)){
				echo '<script>alert("Incorrect Email"); window.location.href = "https://zalea.com";</script>';exit;
			}
		}
		if((isset($_GET['applicationid']) && $_GET['applicationid'] != '') && (isset($_GET['physicianEmail']) && $_GET['physicianEmail'] != '')){
			if(strpos($_SERVER['REQUEST_URI'],'offers') !== false){
				$dealerData = array();
				$dealerData['TransactionType'] = 'reusable_offers_check';
				$dealerData['ApplicationID'] = (int)$_GET['applicationid'];
				$_SESSION['applicationid'] = 'true';
				makeRequestToDV360('pre-qualify',$dealerData);
			}else{
				echo '<script>alert("Incorrect URL"); window.location.href = "https://zalea.com";</script>';exit;
			}
		}else{
			if(isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 1){
				if(strpos($_SERVER['REQUEST_URI'],'redirect') !== false || strpos($_SERVER['REQUEST_URI'],'offers') !== false){
					
				}else{
					wp_redirect( '/offers' );
					exit;
				}
			}else{
				if(strpos($_SERVER['REQUEST_URI'],'app-page-1') !== false || strpos($_SERVER['REQUEST_URI'],'access-offers') !== false || strpos($_SERVER['REQUEST_URI'],'redirect') !== false || strpos($_SERVER['REQUEST_URI'],'offers') !== false || strpos($_SERVER['REQUEST_URI'],'admin-ajax.php') !== false){
					
				}else{
					wp_redirect( '/app-page-1' );
					exit;
				}
			}
		}
	}
	
    if(strpos($_SERVER['HTTP_HOST'],'apply.headwaysales.com') !== false || strpos($_SERVER['HTTP_HOST'],'pr.acornfinance.com') !== false){
		//echo '<script>alert("In session script456");</script>';
		if(strpos($_SERVER['HTTP_HOST'],'kn.pr.acornfinance.com') !== false){
			add_filter( 'pre_option_stylesheet', function() { return 'prime-rates-mykukun'; } );
		}else{
			add_filter( 'pre_option_stylesheet', function() { return 'prime-rates-head-way-sales'; } );
		}
		add_filter( 'pre_option_template', function() { return 'flat-responsive-pro'; } );
		
		if((isset($_GET['applicationid']) && $_GET['applicationid'] != '') && (!isset($_GET['dealerid']) && $_GET['dealerid'] == '')){
			echo '<script>alert("Incorrect URL; Please reach out to your dealer or contractor for the correct URL."); window.location.href = "https://www.acornfinance.com";</script>';exit;
		}
		if("https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] == "https://".$_SERVER['HTTP_HOST']."/app-page-1"){
			$_SESSION['application_url'] = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		if((isset($_GET['dealerid']) && $_GET['dealerid'] != '') || (isset($_POST['dealerid']) && $_POST['dealerid'] != '') || (isset($_GET['av']) && $_GET['av'] != '')){
			$_SESSION['application_url'] = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$dealer_id = '';
			if(isset($_POST['dealerid'])){
				$dealer_id = $_POST['dealerid'];
				// echo "<pre>if condition";print_r($dealer_id);exit;
			}else{
				$dealer_id = isset($_GET['dealerid'])?$_GET['dealerid']:'';
				// echo "<pre>else";print_r($dealer_id);exit;
			}
			
			if(isset($_GET['applicationid']) && $_GET['applicationid'] != ''){
				if(strpos($_SERVER['REQUEST_URI'],'offers') !== false){
					$dealerData = array();
					$dealerData['TransactionType'] = 'reusable_offers_check';
					$dealerData['ApplicationID'] = (int)$_GET['applicationid'];
					$_SESSION['applicationid'] = 'true';
					makeRequestToDV360('pre-qualify',$dealerData);
				}else{
					echo '<script>alert("Incorrect URL; Please reach out to your dealer or contractor for the correct URL."); window.location.href = "https://www.acornfinance.com";</script>';exit;
				}
			}
			$borrower_path = explode('/app-page-1?dealerid=', $_SERVER['REQUEST_URI']);
			
			if(count($borrower_path) == 1){
				$public_path = explode('/?dealerid=', $_SERVER['REQUEST_URI']);

				if (strpos($_SERVER['REQUEST_URI'],'personal-loans/pre-qualify') !== false || (count($public_path) <= 2 && !empty($public_path[1]))) {
					wp_redirect(get_site_url().'/app-page-1?dealerid='.$_GET['dealerid']);
					exit;
				}
			}
		}else if(isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 1){
			
			//echo '<script>alert("In session script");</script>';
			if(strpos($_SERVER['REQUEST_URI'],'offers') !== false || strpos($_SERVER['REQUEST_URI'],'no-offers') !== false || strpos($_SERVER['REQUEST_URI'],'redirect') !== false || (isset($_GET['th']) && $_GET['th'] == 'kn') || $_SESSION['acorn_theme'] == 'mykukun'){
				
			}else{
				wp_redirect( get_site_url().'/offers' );
				exit;
			}
		}else if(isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 0){
			
			//echo '<script>alert("In session script");</script>';
			if(strpos($_SERVER['REQUEST_URI'],'no-offers') !== false || strpos($_SERVER['REQUEST_URI'],'acorn-personal-loans') !== false || strpos($_SERVER['REQUEST_URI'],'offers') !== false || (isset($_GET['th']) && $_GET['th'] == 'kn') || $_SESSION['acorn_theme'] == 'mykukun' || strpos($_SERVER['REQUEST_URI'],'technical-error') !== false){

			}else if(strpos($_SERVER['REQUEST_URI'],'app-page-') !== false){
				if(isset($_SESSION['application_url']) && "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] != $_SESSION['application_url']){
					wp_redirect( $_SESSION['application_url'] );
					exit;
				}
			}else{
				wp_redirect( get_site_url().'/no-offers' );
				exit;
			}
		}else if($_SERVER['REQUEST_URI']==='/offers'  ){
			
			//echo '<script>alert("In session script");</script>';
			wp_redirect(get_site_url(). '/offers');
		}
	}
	
	if(strpos($_SERVER['HTTP_HOST'],'retainfinance.com') !== false){
		//echo '<script>alert("In session script456");</script>';
		add_filter( 'pre_option_stylesheet', function() { return 'retain-finance'; } );
		add_filter( 'pre_option_template', function() { return 'flat-responsive-pro'; } );
		if("https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] == "https://".$_SERVER['HTTP_HOST']."app-page-1"){
			$_SESSION['application_url'] = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		if((isset($_GET['applicationid']) && $_GET['applicationid'] != '') && (!isset($_GET['dealerid']) && $_GET['dealerid'] == '')){
			echo '<script>alert("Incorrect URL; Please reach out to your dealer or contractor for the correct URL."); window.location.href = "https://www.retainfinance.com";</script>';exit;
		}
		if("https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] == "https://".$_SERVER['HTTP_HOST']."/app-page-1"){
			$_SESSION['application_url'] = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		if((isset($_GET['dealerid']) && $_GET['dealerid'] != '') || (isset($_POST['dealerid']) && $_POST['dealerid'] != '')){
			$_SESSION['application_url'] = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$dealer_id = '';
			if(isset($_POST['dealerid'])){
				$dealer_id = $_POST['dealerid'];
				// echo "<pre>if condition";print_r($dealer_id);exit;
			}else{
				$dealer_id = isset($_GET['dealerid'])?$_GET['dealerid']:'';
				// echo "<pre>else";print_r($dealer_id);exit;
			}
			
			if(isset($_GET['applicationid']) && $_GET['applicationid'] != ''){
				if(strpos($_SERVER['REQUEST_URI'],'offers') !== false){
					$dealerData = array();
					$dealerData['TransactionType'] = 'reusable_offers_check';
					$dealerData['ApplicationID'] = (int)$_GET['applicationid'];
					$_SESSION['applicationid'] = 'true';
					makeRequestToDV360('pre-qualify',$dealerData);
				}else{
					echo '<script>alert("Incorrect URL; Please reach out to your dealer or contractor for the correct URL."); window.location.href = "https://www.retainfinance.com";</script>';exit;
				}
			}
			$borrower_path = explode('/app-page-1?dealerid=', $_SERVER['REQUEST_URI']);
			
			if(count($borrower_path) == 1){
				$public_path = explode('/?dealerid=', $_SERVER['REQUEST_URI']);

				if (strpos($_SERVER['REQUEST_URI'],'personal-loans/pre-qualify') !== false || (count($public_path) <= 2 && !empty($public_path[1]))) {
					wp_redirect(get_site_url().'/app-page-1?dealerid='.$_GET['dealerid']);
					exit;
				}
			}
		}else if(isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 1){
			
			//echo '<script>alert("In session script");</script>';
			if(strpos($_SERVER['REQUEST_URI'],'offers') !== false || strpos($_SERVER['REQUEST_URI'],'no-offers') !== false || strpos($_SERVER['REQUEST_URI'],'redirect') !== false){
				
			}else{
				wp_redirect( get_site_url().'/offers' );
				exit;
			}
		}else if(isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 0){
			
			//echo '<script>alert("In session script");</script>';
			if(strpos($_SERVER['REQUEST_URI'],'no-offers') !== false || strpos($_SERVER['REQUEST_URI'],'acorn-personal-loans') !== false || strpos($_SERVER['REQUEST_URI'],'offers') !== false || strpos($_SERVER['REQUEST_URI'],'technical-error') !== false){
				
			}else if(strpos($_SERVER['REQUEST_URI'],'app-page-') !== false){
				if(isset($_SESSION['application_url']) && "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] != $_SESSION['application_url']){
					wp_redirect( $_SESSION['application_url'] );
					exit;
				}
			}else{
				wp_redirect( get_site_url().'/no-offers' );
				exit;
			}
		}else if($_SERVER['REQUEST_URI']==='/offers'  ){
			
			//echo '<script>alert("In session script");</script>';
			wp_redirect(get_site_url(). '/offers');
		}else if(strpos($_SERVER['REQUEST_URI'],'wp-admin/admin-ajax.php') !== false){
			
		}else{
			if(strpos($_SERVER['REQUEST_URI'],'retain-e-sign') !== false || strpos($_SERVER['REQUEST_URI'],'retain-terms-of-use') !== false || strpos($_SERVER['REQUEST_URI'],'retain-privacy-policy') !== false || strpos($_SERVER['REQUEST_URI'],'access-offers') !== false){
				
			}/*else{		
				//wp_redirect(get_site_url(). '/offers');
				echo '<script>alert("Incorrect URL; Please reach out to your dealer or contractor for the correct URL."); window.location.href = "https://www.retainfinance.com";</script>';exit;
			}*/
		}
	}

	// Added this logic for Primerate application page reload 14-09-21
	if(strpos($_SERVER['HTTP_HOST'],'primerates.com') !== false){
		if((isset($_GET['partnerid']) && $_GET['partnerid'] != '') || (isset($_POST['partnerid']) && $_POST['partnerid'] != '') || "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] == "https://".$_SERVER['HTTP_HOST']."/app-page-1"){
			$_SESSION['application_url'] = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}else if(isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 0){
			if(strpos($_SERVER['REQUEST_URI'],'app-page-') !== false){
				if(isset($_SESSION['application_url']) && "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] != $_SESSION['application_url']){
					wp_redirect( $_SESSION['application_url'] );
					exit;
				}
			}
		}
	}
	
}

//custom_theme_switch();
if ( ! is_admin() ) {
	add_action('plugins_loaded','custom_theme_switch');
}