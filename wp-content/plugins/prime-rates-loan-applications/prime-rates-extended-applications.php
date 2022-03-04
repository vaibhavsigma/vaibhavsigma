<?php
define( 'PRIME_RATES_EXTENDED_LOAN_APPLICATIONS_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'extended-loan-app'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_EXTENDED_LOAN_APPLICATIONS_CSS_URL', PRIME_RATES_EXTENDED_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_EXTENDED_LOAN_APPLICATIONS_JS_URL', PRIME_RATES_EXTENDED_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );

function extended_apply_for_loans_shortcode($atts = []){
	
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

function prime_rates_extend_loan_applications_enqueue() {
	global $post, $versionConfigData;
	if(has_shortcode( $post->post_content, 'extend-loan-application')){
	  wp_enqueue_script('prime_rates_extend_loan_applications', PRIME_RATES_EXTENDED_LOAN_APPLICATIONS_JS_URL.'main.60757132.js', array(), 0,false );
	  wp_enqueue_script('prime_rates_loan_applications_google', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyANpZbWlnVftI-HYTH8YdmqDBbjIvUndOs&libraries=places', array(), 0,false );

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

function prime_rates_extend_loan_applications_shortcodes_init(){
	add_shortcode('extend-loan-application', 'extended_apply_for_loans_shortcode');
}

function gds_los_upload_documents(){
	global $configData;
	$record_id = '5e87a5d8cdf5af91d32f3f92';
	if(isset($_SESSION['record_id'])){$record_id = $_SESSION['record_id'];}
	$countfiles = count($_FILES['required_documents']['name']);
	$response_data = array();
	// Looping all files
	for($i=0;$i<$countfiles;$i++){

		$filename = $_FILES['required_documents']['name'][$i];	 
		$filedata = file_get_contents($_FILES['required_documents']['tmp_name'][$i]);
		$filetype = $_FILES['required_documents']['type'][$i];
		
		//$filearr = array($_FILES['required_documents']['tmp_name'][$i],$_FILES['required_documents']['name'][$i],$_FILES['required_documents']['type'][$i]);
		//print_r($filearr);
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://papp-cc-dev.api-dataview360.com/GDSLINK/payroll_assistance_program/attachments_api/upload/".$record_id."/".$filename."/YXR0YWNobWVudHN1c2VyQGdkc2xpbmsuY29t%7CiyxW41c1uhj_Rvp6bPZN.json",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $filedata,
		  CURLOPT_HTTPHEADER => array(
			"Content-Type: ".$filetype
		  ),
		));

		$response = curl_exec($curl);
		$response_data[] = json_decode($response,true);
		curl_close($curl);
		//echo $response;
	}
	//print_r($response_data);exit;

	// Making the DV360 Call to update application
	$document_curl = New Curl();
	$dvConfigData = (object)$configData['dv360'];
	//$url=$dvConfigData->post_url;
	$url= "https://" . $_SERVER['HTTP_HOST'] . $dvConfigData->post_url;
	$document_data['TransactionType'] = "LOSApplicationUpdate";
	$document_data['data'] = $response_data;
	//print_r($document_data['data']);
	$document_data_result = $document_curl->callApi($url, 'POST','application/json',array('Request'=>$document_data));
	//print_r($document_data_result);
	wp_redirect("https://".$_SERVER["HTTP_HOST"]."/ppp-application/complete"); exit;
}
add_action( 'wp_ajax_gds_los_upload_documents', 'gds_los_upload_documents' );
add_action( 'wp_ajax_nopriv_gds_los_upload_documents', 'gds_los_upload_documents' );

add_action('init', 'prime_rates_extend_loan_applications_shortcodes_init');
add_action( 'wp_enqueue_scripts', 'prime_rates_extend_loan_applications_enqueue', 99);
