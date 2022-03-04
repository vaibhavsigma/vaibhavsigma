<?php
define( 'PRIME_RATES_SME_APPLICATIONS_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'sme-app'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_SME_APPLICATIONS_JS_URL', PRIME_RATES_SME_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );

function sme_apply_for_loans_shortcode($atts = []){
	
	$atts = (array)$atts;
	$variables = "";

	// Check for offer available session variable
    /*if (isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 1){
		if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){
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

	// Custom URI parameter for checking the headway sales parameter
	if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){
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
		
	echo $jsString.'</script>';
	?>
		<div class="main-cont" id="root"></div>
	<?php
}

function prime_rates_sme_applications_enqueue() {
	global $post, $versionConfigData;
	if(has_shortcode( $post->post_content, 'sme-application')){
	  wp_enqueue_script('prime_rates_ppp_applications', PRIME_RATES_SME_APPLICATIONS_JS_URL.'main.d705bbde.js', array(), 0,false );
	  wp_enqueue_script('prime_rates_loan_applications_google', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyANpZbWlnVftI-HYTH8YdmqDBbjIvUndOs&libraries=places', array(), 0,false );
	  setcookie('isSME','1',0,'/','api-dataview360.com');
	  //$_SESSION['isSME'] = 'true';
	  // Bank specific css loading
	  if(isset($versionConfigData['domains']) && !empty($versionConfigData['domains'])){
			foreach($versionConfigData['domains'] as $host){
				if(isset($host['hosts']) && is_array($host['hosts'])){
					foreach($host['hosts'] as $host_url){
						if($_SERVER['HTTP_HOST'] == $host_url['host']){
							wp_enqueue_style($host['slug'].'_sme_css',get_stylesheet_directory_uri().'/css/Breadcrumb/'.$host['sme_css']);
							wp_enqueue_style($host['slug'].'_sme_transition_css',get_stylesheet_directory_uri().'/css/'.$host['sme_transition_css']);
						}
					}
				}
			}
	  }
	} 
}

function prime_rates_sme_applications_shortcodes_init(){
	add_shortcode('sme-application', 'sme_apply_for_loans_shortcode');
}

add_action('init', 'prime_rates_sme_applications_shortcodes_init');
add_action( 'wp_enqueue_scripts', 'prime_rates_sme_applications_enqueue', 99);
