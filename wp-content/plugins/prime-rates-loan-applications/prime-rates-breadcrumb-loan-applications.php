<?php
define( 'PRIME_RATES_BREADCRUMB_LOAN_APPLICATIONS_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'breadcrumb'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_BREADCRUMB_LOAN_APPLICATIONS_CSS_URL', PRIME_RATES_BREADCRUMB_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_BREADCRUMB_LOAN_APPLICATIONS_JS_URL', PRIME_RATES_BREADCRUMB_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );

function breadcrumb_apply_for_loans_shortcode($atts = []){
	
	// Destroy session if reapplying
	//if(strpos($_SERVER['REQUEST_URI'],'app-page-1') !== false){
		$_SESSION['isOfferAvailable'] = 0;
		unset($_SESSION['mndSuffix']);
	//}
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
		if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"codepen.io") !== false){
			$offer_url = get_site_url(). '/offers';
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
	if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"codepen.io") !== false){
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
	//$jsString .= 'window.isOfferAvailable = "'.$isOfferAvailable.'";';
	$jsString .= 'window.isOfferAvailable = "0";';
	
	if($_POST){
		$variables = $_POST;
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
		<div class="main-cont" id="root"></div>
	<?php
}

function prime_rates_breadcrumb_loan_applications_enqueue() {
	global $post;
	if(has_shortcode( $post->post_content, 'breadcrumb-apply-for-loans')){
	  wp_enqueue_script('prime_rates_breadcrumb_loan_applications', PRIME_RATES_BREADCRUMB_LOAN_APPLICATIONS_JS_URL.'main.43cc03c9.js', array(), 0,false );
	  wp_enqueue_script('prime_rates_loan_applications_google', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyB_bGYpM1GXZaS-a3kivfDQXoP038sQmW8&libraries=places', array(), 0,false );
	  // Primerates specific css loading
	  if(strpos($_SERVER["HTTP_HOST"],"primerates.com") !== false){
		wp_enqueue_style('primerates_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/BCStyles-primerates.css');
		wp_enqueue_style('primerates_transition_css',get_stylesheet_directory_uri().'/css/transition-primerate.css');
	  }
	  // Zalea specific css loading
	  if(strpos($_SERVER["HTTP_HOST"],"zalea.com") !== false){	
		wp_enqueue_style('zalea_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/BCStyles-zalea.css');
		wp_enqueue_style('zalea_transition_css',get_stylesheet_directory_uri().'/css/transition-zalea.css');
	  }
	  // Acorn specific css loading
	  if((strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false) && strpos($_SERVER["HTTP_HOST"],"kn.pr.acornfinance.com") === false){
		wp_enqueue_style('headway_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/BCStyles-headway.css');
		wp_enqueue_style('headway_transition_css',get_stylesheet_directory_uri().'/css/transition-headway.css');  
		wp_enqueue_style('headway_rangeslider_css',get_stylesheet_directory_uri().'/css/acorn-range-slider.css');
	  }
	  
	  // Kukun specific css loading
	  if(strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"kn.pr.acornfinance.com") !== false){
		wp_enqueue_style('headway_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/BCStyles-kukun.css');
		wp_enqueue_style('headway_transition_css',get_stylesheet_directory_uri().'/css/transition-headway.css');
		wp_enqueue_style('kukun_rangeslider_css',get_stylesheet_directory_uri().'/css/kukun-range-slider.css');		
	  }
	  
	  // Retain specific css loading
	  if(strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
		wp_enqueue_style('headway_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/BCStyles-retain.css');
		wp_enqueue_style('headway_transition_css',get_stylesheet_directory_uri().'/css/transition-headway.css');
		wp_enqueue_style('retain_rangeslider_css',get_stylesheet_directory_uri().'/css/retain-range-slider.css');		
	  }
	  
	  // Bank specific css loading
		if(isset($versionConfigData['domains']) && !empty($versionConfigData['domains'])){
			foreach($versionConfigData['domains'] as $host){
				if(isset($host['hosts']) && is_array($host['hosts'])){
					foreach($host['hosts'] as $host_url){
						if($_SERVER['HTTP_HOST'] == $host_url['host']){
							wp_enqueue_style($host['slug'].'_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/'.$host['breadcrumb_css']);
							wp_enqueue_style($host['slug'].'_breadcrumb_transition_css',get_stylesheet_directory_uri().'/css/'.$host['breadcrumb_transition_css']);
						}
					}
				}
			}
		}
	} 
}

function prime_rates_breadcrumb_loan_applications_shortcodes_init(){
	add_shortcode('breadcrumb-apply-for-loans', 'breadcrumb_apply_for_loans_shortcode');
}

add_action('init', 'prime_rates_breadcrumb_loan_applications_shortcodes_init');
add_action( 'wp_enqueue_scripts', 'prime_rates_breadcrumb_loan_applications_enqueue', 99);
