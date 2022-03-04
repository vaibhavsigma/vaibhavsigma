<?php
define( 'PRIME_RATES_MULTIPAGE_LOAN_APPLICATIONS_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'multipage'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_MULTIPAGE_LOAN_APPLICATIONS_CSS_URL', PRIME_RATES_MULTIPAGE_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_MULTIPAGE_LOAN_APPLICATIONS_JS_URL', PRIME_RATES_MULTIPAGE_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );

function multipage_apply_for_loans_shortcode($atts = []){
	
	$atts = (array)$atts;
	$variables = "";
	if(isset($atts['step'])){
		$stepNo = (int)$atts['step'];
	}else{
		$stepNo =0;
	}
	if($stepNo>1 && !isset($_SESSION['multi_step_data']) && !$_POST){
		$url = get_site_url(). '/personal-loans/app-form-start';
		?>
		<script type="text/javascript">window.location.href = '<?php echo $url; ?>';</script>
		<?php
		//wp_redirect( $url );
		exit;
	}
	if(isset($atts['multi-step']) && strtolower($atts['multi-step']) == 'yes'){
		$loadMultiStepForm = 1;
	}else{
		$loadMultiStepForm = 0;
	}
	// Check for offer available session variable
    if (isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 1){
		if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false){
			$offer_url = get_site_url(). '/offers';
		}else{
			$offer_url = get_site_url(). '/personal-loans/pre-qualify/offers';
		}
		?>
		<script type="text/javascript">window.location.href = '<?php echo $offer_url; ?>';</script>
		<?php
		//wp_redirect( $offer_url );
		exit;
	}else{
		$isOfferAvailable = '0';
	}
	$jsString = '<script type="text/javascript">';
	$jsString .= 'window.isMultiStep = '.$loadMultiStepForm.';';
	// Custom URI parameter for checking the headway sales parameter
	if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false){
		$jsString .= 'window.isHeadway = "1";';
	}else{
		$jsString .= 'window.isHeadway = "0";';
	}
	if(strpos($_SERVER['REQUEST_URI'],'personal-loans/access-offers') !== false){
		$jsString .= 'window.isAccessOffers = "1";';
	}
	if(isset($_SESSION['accessOffersCount'])){
		$jsString .= 'window.attemptCount = "'.$_SESSION['accessOffersCount'].'";';
	}else{
		$jsString .= 'window.attemptCount = "0";';
	}
	if(isset($_SESSION['expired_result']) && $_SESSION['expired_result'] != ""){
		$jsString .= 'window.lenderResponse = '.$_SESSION['expired_result'].';';
	}
	if(isset($_SESSION['dealerid'])){
		$jsString .= 'window.dealerid = "'.$_SESSION['dealerid'].'";';
	}
	$jsString .= 'window.isOfferAvailable = "'.$isOfferAvailable.'";';
	
	if($_POST){
		multi_step_form_submit($_POST);
		if(isset($_SESSION['multi_step_data'])){
			$variables = $_SESSION['multi_step_data'];
		}else{
			$variables = $_POST;
		}
	}else if(isset($_SESSION['multi_step_data'])){
			$variables = $_SESSION['multi_step_data'];
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
	?>
		<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/Multipage/multipage-primerates.css" rel="stylesheet">
		<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/transition-primerate.css" rel="stylesheet">
		<div class="main-cont" id="root"></div>
	<?php
}

function prime_rates_multipage_loan_applications_enqueue() {
	global $post;
	if(has_shortcode( $post->post_content, 'multipage-apply-for-loans')){
	  wp_enqueue_script('prime_rates_multipage_loan_applications', PRIME_RATES_MULTIPAGE_LOAN_APPLICATIONS_JS_URL.'main.36685681.js', array(), 0,false );  
	  wp_enqueue_script('prime_rates_loan_applications_google', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyANpZbWlnVftI-HYTH8YdmqDBbjIvUndOs&libraries=places', array(), 0,false );
	} 
}

function prime_rates_multipage_loan_applications_shortcodes_init(){
	add_shortcode('multipage-apply-for-loans', 'multipage_apply_for_loans_shortcode');
}

function multi_step_form_submit($postData){
	if($postData){
		if(isset($_SESSION['multi_step_data'])){
			$prevData = $_SESSION['multi_step_data'];
			$allData = array_merge($prevData,$postData);
			$_SESSION['multi_step_data'] = $allData;
			if(isset($_SESSION['multi_step_data']['previous_step']) && $_SESSION['multi_step_data']['previous_step']==3){
				makeRequestToDV360('pre-qualify',$_SESSION['multi_step_data']);
				unset($_SESSION['multi_step_data']);
			}
		}else{
			$_SESSION['multi_step_data'] = $postData;
			/*$_SESSION['multi_step_data']['previous_step']  = 1;
			$url = get_site_url(). '/personal-loans/app-form-housing';
			wp_redirect( $url );
			exit;*/
		}
	}
}
add_action('init', 'prime_rates_multipage_loan_applications_shortcodes_init');

add_action( 'wp_ajax_multi_step_form_submit', 'multi_step_form_submit' );
add_action( 'wp_ajax_nopriv_multi_step_form_submit', 'multi_step_form_submit' );
add_action( 'wp_enqueue_scripts', 'prime_rates_multipage_loan_applications_enqueue' );
