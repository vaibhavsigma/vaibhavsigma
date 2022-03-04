<?php
define( 'PRIME_RATES_OFFERS_PAGE_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'pr-offers'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_OFFERS_PAGE_CSS_URL', PRIME_RATES_OFFERS_PAGE_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_OFFERS_PAGE_JS_URL', PRIME_RATES_OFFERS_PAGE_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );

function pre_qualified_offers_shortcode(){
	// This is the root element for the React offer page build
	$html = '<div class="main-cont" id="root"></div>';

	// We have added this session variable to check if it is a dealer accessed offer page
	if(isset($_SESSION['applicationid'])){
		$html .= '<script>window.disableClaimOffer=true;</script>';
	}

	// Based on how the page is loaded means if it is from application to offer page post submit or just offer page
	// reload which returns the rendering html variable
	if($_POST){
		// Added this condition for checking application submit and not the offer page refresh
		//$_POST['isPost'] = 'true';
		makeRequestToDV360('pre-qualify',$_POST);
		// Added shortcode enqueue script below to load build number variable dynamically
		global $post,$buildNumber;
		if(has_shortcode( $post->post_content, 'pre-qualified-offers') && ($_POST || (isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 1))){
			$action = 'mandrill_incomplete';
			wp_enqueue_script('prime_rates_loan_applications', PRIME_RATES_OFFERS_PAGE_PLUGIN_URL.$buildNumber, array(), 0,false );
		}
	}else{
		// If it is not post request than checking for alternate flows and giving response accordingly
		global $post,$buildNumber;
		if(has_shortcode( $post->post_content, 'pre-qualified-offers') && isset($_GET['applicationid'])){
			$action = 'mandrill_incomplete';
			wp_enqueue_script('prime_rates_loan_applications', PRIME_RATES_OFFERS_PAGE_PLUGIN_URL.$buildNumber, array(), 0,false );
		}
		if(isset($_SESSION['isAccessOffer']) && $_SESSION['isAccessOffer'] == 'true' && !isset($_GET['applicationid'])){
			if(strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
				wp_redirect( "https://".$_SERVER["HTTP_HOST"]."/app-page-1?dealerid=".$_SESSION['dealerid'] );
			}else{
				wp_redirect( "https://".$_SERVER["HTTP_HOST"]."/personal-loans/app-page-1" );
			}
			session_destroy();
			session_unset();
			exit;
		}else if(isset($_SESSION[session_id().'-formData']) && !empty($_SESSION[session_id().'-formData'])){
			makeRequestToDV360('pre-qualify',$_SESSION[session_id().'-formData']);
		}else{
			wp_redirect( get_site_url() );
			exit;
		}
	} return $html;
	 
}

// This is the DV360 API call handler function
function makeRequestToDV360($parentPage,$postData){
	global $configData,$PLconfigData,$dvConfigData,$partner_theme_config,$versionConfigData,$buildNumber,$buildCSS;
	
	if(!session_id()){session_start();}
	$hSessionID= session_id();
	
	// Check to see if its multipage request and session variables available if not available than redirect to 1st page of multi-page form
	if(isset($postData['action']) && $postData['action'] == 'multi_step_form_submit' && !isset($postData['loanamount'])){
		echo '<script>alert("Sorry, your session has expired. Please complete the loan application again.");</script>';
		$multi_url = get_site_url(). '/personal-loans/app-form-start';
		wp_redirect( $multi_url );
		exit;
	}

	// Check to see if proper values are passed or not from the frontend application from server end as per ticket #PM-2147
	if((isset($postData['loanamount']) && $postData['loanamount'] == '') || (isset($postData['birthdate']) && $postData['birthdate'] == '')){
		$log_msg = New Curl();
		$log_msg->logMessages('Continue - Technical Error Validation',true);
		$error_url = get_site_url(). '/technical-error/';
		wp_redirect( $error_url );
		exit;
	}
	
	$isOfferAvailable = false;
    $_SESSION['isOfferAvailable'] = $isOfferAvailable;
	
	$userSessionId = session_id();
    if (!isset($_SESSION[session_id().'-formData'])){
        $_SESSION[session_id().'-formData'] = $postData;
    }else{
		// Set condition for reusable offers to use new form post data every time
		/*if((isset($postData['TransactionType']) || isset($postData['transactionType'])) && ($postData['TransactionType'] == 'reusable_offers_check' || $postData['TransactionType'] == 'addCoBorrower' || $postData['transactionType'] == 'addCoBorrower')){
			$postData =  $postData;
		}else{
			$postData =  $_SESSION[session_id().'-formData'];
		}*/
    }
    $session_id = @$postData['uuid'].'-'.$userSessionId;
	$_SESSION['request_id'] = $session_id;
	$noOfferURL = "";
	if(isset($postData['affiliateid']) && $postData['affiliateid'] == "426464"){
		$query = explode('?', $configData['noOfferRedirection']['acquireNoOfferRedirectURL']); 
		parse_str($query[1], $data_arr); // Parse the query string into an array	
		$data_arr['AFID'] = $postData['affiliateid'];
		$noOfferURL = $query[0].'?'.http_build_query($data_arr);
	}else if(isset($postData['TransactionType']) && $postData['TransactionType'] == "leadCheckApp"){
		$query = explode('?', $configData['noOfferRedirection']['acquireNoOfferRedirectURL']); 
		parse_str($query[1], $data_arr); // Parse the query string into an array	
		$data_arr['AFID'] = $postData['affiliateid'];
		$noOfferURL = $query[0].'?'.http_build_query($data_arr);
	}else{
		$query = explode('?', $configData['noOfferRedirection']['defaultNoOfferRedirectURL']); 
		parse_str($query[1], $data_arr); // Parse the query string into an array	
		if(isset($postData['affiliateid']) && $postData['affiliateid'] != ""){
			$data_arr['AFID'] = $postData['affiliateid'];
			$noOfferURL = $query[0].'?'.http_build_query($data_arr);
		}else{
			$noOfferURL = $configData['noOfferRedirection']['defaultNoOfferRedirectURL'];
		}
	}

    // Used to redirect on offer landing page.
    if (!isset($_SESSION['offerAvailableArray'])){
        $_SESSION['offerAvailableArray'] = array();
    }
	
    if (isset($_SESSION[$session_id]) && !empty($_SESSION[$session_id])){
        $result = $_SESSION[$session_id];
        $_SESSION['offerAvailableArray'] = array_merge($_SESSION['offerAvailableArray'],array(session_id()));
        // Check with offer set into session or not.
        if(isset($result->Response->Offers) && !empty($result->Response->Offers)){
            $isOfferAvailable = true;
            $_SESSION['isOfferAvailable'] = 1;
        }else{
            $_SESSION['isOfferAvailable'] = 0;
        }
    }
	
    $variables = (object)$postData;
	
	//fetch from session hsessionid
	if(isset($variables->hsessionid)){
		$data['hsessionid'] = $variables->hsessionid;
	}
	if(isset($postData['isPost']) && $postData['isPost'] == 'true'){
		$isOfferAvailable = false;
	}
	// Offer not set into session then call the dv360 otherwise else
    if (!$isOfferAvailable || isset($_GET['applicationid']) || $postData['TransactionType'] == 'addCoBorrower' || $postData['transactionType'] == 'addCoBorrower'){ // Added additional condition for Headway dealers to lookup another application id withing same user session - PM-730 - 08-08-2018
		if(isset($configData['dv360'])){
		// echo "<pre>";print_r($configData);exit;
		// echo "<pre>";print_r($configData);
		// echo "<br/>";print_r($configData['dv360']);echo $configData['dv360']['post_url'];echo "<br/>";echo "Test-URL";echo "<br/>";
		// echo "</pre>";
			$dvConfigData = (object)$configData['dv360'];
			$curl = new Curl();
			// Added condition for Mock API URL
			if(isset($partner_theme_config['hosts'])){
				$url=$dvConfigData->mock_url;
			}else{
				$url=$dvConfigData->post_url;
			}
			$method = 'POST';
			$data = $postData;
			if(isset($data['TransactionType']) && $data['TransactionType'] == "reusable_offers_check"){

				/*if(!isset($_SESSION['accessOffersCount']) && empty($_SESSION['accessOffersCount'])){
					$offer_access_cnt = 0;
					$_SESSION['accessOffersCount'] = $offer_access_cnt;
				}else{
					if($_SESSION['accessOffersCount'] == 3){
						$_SESSION['accessOffersCount'] = 0;
					}
				}*/
				$data['TransactionType'] = 'reusable_offers_check';
			}else if(isset($data['TransactionType']) && $data['TransactionType'] == "leadCheckApp"){
				$data['TransactionType'] = 'leadCheckApp';
				unset($data['loanPurpose']);
				unset($data['loanpurpose']);
			}else{
				$data['TransactionType'] = 'Application';
			}
			$offerApr_count = 0;
			if($data['TransactionType'] == "reusable_offers_check"){
				//$data['email'] = 'pankaj.sahani@sigmainfo.net';
				//$data['ssn'] = '4418';
				//echo '<pre>';print_r($data);exit;
				
				//Check if given email user login exists with domain extension
				if($partner_theme_config){
					$user = get_user_by('login',$postData['email']."_".$partner_theme_config['slug']);
					if($user){
						$_SESSION['borrower_email'] = $postData['email'];
						//Check for account verified for the given email user
						$redirect_url = '';
						if ( get_user_meta( $user->ID, 'has_to_be_activated', true ) != false ) {
							$redirect_url = 'https://'.$_SERVER['HTTP_HOST'].'/borrower-login?verified=false';
						}else{
							$redirect_url = 'https://'.$_SERVER['HTTP_HOST'].'/borrower-login?verified=true';
						}
						wp_redirect($redirect_url);
						exit;
					}
				}
				if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
					$data['affiliateid'] = 'Headway';
				}else{
					$data['affiliateid'] = '426858';
				}
				$_SESSION['borrower_email'] = $postData['email'];

				// Added this mock offers loading from JSON logic for testing
				if($data['email'] == 'acorntest@acornfinance.com'){
					$acorn_test = readJson(PRIME_RATES_LOAN_APPLICATIONS_JSON_DIR.'mock-api/'.$data['ssn'].'-offers-test-app.json');
					$result = json_decode(json_encode($acorn_test));
					//print_r($result);exit('call');
					$variables->loanamount = $result->loanamount;
					$variables->loanpurpose = $result->loanpurpose;
				}else{
					$result = $curl->callApi($url, $method,'application/json',array('Request'=>$data));
					if( isset($result->Response->ApplicationID) && $result->Response->ApplicationID == "" ){
						$result->Response->ApplicationID = $result->Response->ApplicationInfo->ApplicationID;
					}
				}
				$_SESSION['isAccessOffer'] = 'true';
				if(isset($result->Response->ApplicationInfo->ApplicationID) && $result->Response->ApplicationInfo->ApplicationID != ''){
					$ApplicationID = $result->Response->ApplicationInfo->ApplicationID;
				}else{
					$ApplicationID = undefined;
				}
				if(isset($result->Response->ApplicationInfo->uuid) && $result->Response->ApplicationInfo->uuid != ''){
					$uuid = $result->Response->ApplicationInfo->uuid;
				}else{
					$uuid = undefined;
				}
				$_SESSION['UUID'] = $uuid;
				$_SESSION['ApplicationID'] = $ApplicationID;
				$_SESSION['Outcome'] = $result->Response->outcome;
				$_SESSION['dealerid'] = isset($data['dealerid'])?$data['dealerid']:'';
				if(isset($result->Response->Offers) && !empty($result->Response->Offers) && strtolower($result->Response->outcome) == "offers available"){
					// Delete accessoffer count cookie if get offers
					setcookie('accessOffersCount', 0, time() - 3600, "/");
					// Delete offer URL for application id request
					if(isset($_SESSION['applicationid'])){
						for($i = 0; $i < count($result->Response->Offers); $i++){
							$result->Response->Offers[$i]->OfferURL = "";
						}
					}
					for($i = 0; $i < count($result->Response->Offers); $i++){
						if($result->Response->Offers[$i]->APR > 0){
							$offerApr_count++;
						}
					}
					
					// Commenting out below as per requirement PM-1902
					$_SESSION['isOfferAvailable'] = 1;
					$_SESSION[$session_id] = $result;
					$_SESSION[session_id().'-formData'] = $postData;
					$_SESSION['offerAvailableArray'] = array_merge($_SESSION['offerAvailableArray'],array(session_id()));
					
				}else if(isset($result->Response->outcome) && strtolower($result->Response->outcome) == "application expired"){
					$_SESSION['isOfferAvailable'] = 0;
					$_SESSION['expired_result'] = json_encode($result);

					if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
						$access_url = get_site_url(). '/access-offers-expired?applicationDate='.$result->Response->ApplicationInfo->ApplicationDate.'&loanAmount='.$result->Response->ApplicationInfo->loanamount.'&loanPurpose='.$result->Response->ApplicationInfo->loanpurpose;
					}else{
						$access_url = get_site_url(). '/personal-loans/access-offers-expired';
					}
					wp_redirect( $access_url );
					exit;
				}else if(isset($result->Response->outcome) && strtolower($result->Response->outcome) == "no offers available"){
					$_SESSION['isOfferAvailable'] = 0;
					$_SESSION['accessOffers-no-offers'] = 1;
					$_SESSION['no-offers-ApplicationDate'] = $result->Response->ApplicationInfo->ApplicationDate;
					$_SESSION['no-offers-loanpurpose'] = $result->Response->ApplicationInfo->loanpurpose;
					$_SESSION['no-offers-loanamount'] = $result->Response->ApplicationInfo->loanamount;
					$_SESSION['no-offers-applicationid'] = $result->Response->ApplicationInfo->ApplicationID;
					if(!isset($_COOKIE['accessOffersCount']) && empty($_COOKIE['accessOffersCount'])){
						$offer_access_cnt = 1;
						//$_SESSION['accessOffersCount'] = $offer_access_cnt;
						setcookie('accessOffersCount',$offer_access_cnt,time()+900,"/");
					}else{
						$offer_access_cnt = $_COOKIE['accessOffersCount']+1;
						setcookie('accessOffersCount',$offer_access_cnt,time()+900,"/");
					}
					if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
						$access_url = get_site_url(). '/access-offers';
					}else{
						$access_url = get_site_url(). '/personal-loans/access-offers-no-offers';
					}
					wp_redirect( $access_url );
					exit;
				}else{
					if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
						$_SESSION['isOfferAvailable'] = 0;
						$_SESSION['accessOffers-no-offers'] = 1;
						//$_SESSION['accessOffersCount']++;
						if(!isset($_COOKIE['accessOffersCount']) && empty($_COOKIE['accessOffersCount'])){
							$offer_access_cnt = 1;
							//$_SESSION['accessOffersCount'] = $offer_access_cnt;
							setcookie('accessOffersCount',$offer_access_cnt,time()+900,"/");
						}else{
							$offer_access_cnt = $_COOKIE['accessOffersCount']+1;
							setcookie('accessOffersCount',$offer_access_cnt,time()+900,"/");
						}
						$access_url = get_site_url(). '/access-offers';
						wp_redirect( $access_url );
						exit;
					}else{
						$_SESSION['isOfferAvailable'] = 0;
						//$_SESSION['accessOffersCount']++;
						$access_url = get_site_url(). '/personal-loans/access-offers';
						wp_redirect( $access_url );
						exit;	
					}
				}
			}else{
				//echo '<pre>';print_r(json_encode(array('Request'=>$data)));
				if(isset($data['housingPayment']) && $data['housingPayment'] == ""){
					$data['housingPayment'] = "0";
				}
				$data['loanPurpose'] = $data['loanpurpose'];
				
				if(!isset($data['dealerid']) && strpos($_SERVER["HTTP_HOST"],"primerates.com") !== false){
					$variables->dealerid = 'PRS-WEBSITE';
				}	
				// Added for Primerates PM-1855
				if(strpos($_SERVER["HTTP_HOST"],"primerates.com") !== false){
					$data['affiliateID'] = '426858';
					$data['affiliateid'] = '426858';
				}				
				// Added for retain finance PM-1825
				if(strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
					if(isset($data['utm_source']) && $data['utm_source'] == ''){
						$data['utm_source'] = 'Retain-Finance';
					}
					if(isset($data['utm_campaign']) && $data['utm_campaign'] == ''){
						$data['utm_campaign'] = 'Private-Label';
					}
				}
				
				if(strpos($_SERVER['REQUEST_URI'],'5point') !== false){
					$data['TransactionType'] = "LOSApplication5Point";
				}
				if(strpos($_SERVER['HTTP_HOST'],'los-qa-uk.api-dataview360.com') !== false){
					$data['TransactionType'] = "LOSApplicationUK";
				}
				if(strpos($_SERVER['HTTP_HOST'],'los-qa-uk-ht.api-dataview360.com') !== false){
					$data['TransactionType'] = "LOSApplicationHT";
				}
				//echo '<pre><script>alert(window.loanPurpose);</script>';print_r($data);exit;

				// Stripping special character backslashes from address field
				$data['streetaddress'] = stripcslashes($data['streetaddress']);
				$data['appartment'] = stripcslashes($data['appartment']);
				$data['city'] = stripcslashes($data['city']);

				$result = $curl->callApi($url, $method,'application/json',array('Request'=>$data));
				//print_r($result);exit;
				
				if($result->http_code!=200 && $result->http_code!=100){
					$cnt = 1;
					do {
						$result = $curl->callApi($url, $method,'application/json',array('Request'=>$data));
						$cnt++;
					} while ( $cnt <= $dvConfigData->attempt_cnt_on_error && $result->http_code != 200 && $result->http_code != 100 );
				}
				//print_r($result->Response);exit;
				if (isset($result) && !empty($result->Response->Offers)){
					if(isset($result->Response->dupCheckResult) && $result->Response->dupCheckResult == "no offers available"){
						$_SESSION['no-offers-ApplicationDate'] = $result->Response->most_recent_application_datetime;
						$_SESSION['no-offers-loanpurpose'] = $result->Response->most_recent_application_loan_purpose;
						$_SESSION['no-offers-loanamount'] = $result->Response->most_recent_application_loan_amount;
						$_SESSION['no-offers-applicationid'] = $result->Response->lastApplicationID;
						$_SESSION['isOfferAvailable'] = 0;
						
						if($configData['system']['mandrill']['sendToMandrill']==true){
							$headway_mail['firstname'] = isset($variables->firstname)?$variables->firstname:'';
							$headway_mail['lastname'] = isset($variables->lastname)?$variables->lastname:'';
							$headway_mail['email'] = isset($variables->email)?$variables->email:'';
							$headway_mail['agreeemail'] = 'true';
							$headway_mail['is_offer'] = 'no';
							$headway_mail['dealerid'] = isset($variables->dealerid)?$variables->dealerid:'';
							$headway_mail['loanpurpose'] = isset($variables->loanpurpose)?$variables->loanpurpose:'';
							headway_mailchimp($headway_mail);
						}
						?>
						<script>
							window._paq.push(['trackEvent','PLPQ','PLPQ No Offers', 0]);
						</script>
						<?php
					}else{	
						if(isset($partner_theme_config) && is_array($partner_theme_config['hosts'])){
							foreach($partner_theme_config['hosts'] as $host_url){
								if($_SERVER['HTTP_HOST'] == $host_url['host']){
									if(isset($postData['TransactionType']) && $postData['TransactionType'] == 'addCoBorrower'){
										if(isset($_SESSION['borrower_email']) && $_SESSION['borrower_email'] != ''){
											$variables->email = $_SESSION['borrower_email'];
										}
										$variables->loanamount = $_SESSION['loanamount'];
										$variables->loanpurpose = $_SESSION['loanpurpose'];
										$variables->firstname = $_SESSION['firstname'];
										$variables->lastname = $_SESSION['lastname'];
									}else if(isset($postData['transactionType']) && $postData['transactionType'] == 'addCoBorrower'){
										if(isset($_SESSION['borrower_email']) && $_SESSION['borrower_email'] != ''){
											$variables->email = $_SESSION['borrower_email'];
										}
										$variables->loanamount = $_SESSION['loanamount'];
										$variables->loanpurpose = $_SESSION['loanpurpose'];
										$variables->firstname = $_SESSION['firstname'];
										$variables->lastname = $_SESSION['lastname'];
									}else{
										if(isset($variables->email) && $variables->email != ''){
											$_SESSION['borrower_email'] = $variables->email;
										}
										$_SESSION['loanSubPurpose'] = $variables->loanSubPurpose;
										$_SESSION['tradingInVehicle'] = $variables->tradingInVehicle;
										$_SESSION['employmentStatus'] = $variables->employmentStatus;
										$_SESSION['loanamount'] = $variables->loanamount;
										$_SESSION['loanpurpose'] = $variables->loanpurpose;
										$_SESSION['firstname'] = $variables->firstname;
										$_SESSION['lastname'] = $variables->lastname;
									}
								}
							}
							//print_r($partner_theme_config);exit;
						}else if(strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
							// Retain Call
							if(isset($variables->email) && $variables->email != ''){
								$_SESSION['borrower_email'] = $variables->email;
								$_SESSION['borrower_firstname'] = $variables->firstname;
							}
							$variables->loanpurpose = 'Legal Expenses';
							$objMailChimpNewRetain = NEW MailChimp();
							$objMailChimpNewRetain->listId = $configData['system']['mail-chimp']['retain-newsletter-list-id']; // Retain Newsletter - List ID
							$offers = $result->Response->Offers;
							$lend_array = array();
							foreach($offers as $offer){
								$lend_array[] = $offer->Lender;
							}
							$lend_array = array_unique($lend_array);
							if($data['email']=='' && isset($result->Response->ApplicationInfo->email) && $result->Response->ApplicationInfo->email !='' ){
								$_SESSION['user_email'] = $result->Response->ApplicationInfo->email;
								$data['email'] = $_SESSION['user_email'];
							}
							$offer_count = 0;
							for($i = 0; $i<count($result->Response->Offers); $i++){
								if($result->Response->Offers[$i]->displayOffer == "true"){
									$offer_count++;
								}
								if($result->Response->Offers[$i]->APR > 0){
									$offerApr_count++;
								}
							}
							$data['offers'] = 'true';
							$data['application_date'] = date('m/d/Y');
							$data['offer_selected_date'] = '';
							// Added AB Test Group logic as per PM-2065
							$test_array = $configData['system']['testGroup'];
							$rand_keys = array_rand($test_array,1);
							$data['testGroup'] = $test_array[$rand_keys];
							$data['application_id'] = $result->Response->ApplicationID;
							$data['cb_indicator'] = $result->Response->cb_indicator;
							$data['number_of_lenders'] = count($lend_array);
							$data['number_of_offers'] = count($result->Response->Offers);
							
							if($data['creditscore'] == "720-850"){
								$data['applicant_credit_score'] = "Excellent";
							}
							if($data['creditscore'] == "680-719"){
								$data['applicant_credit_score'] = "Good";
							}
							if($data['creditscore'] == "620-679"){
								$data['applicant_credit_score'] = "Average";
							}
							if($data['creditscore'] == "350-619"){
								$data['applicant_credit_score'] = "Poor";
							}
							//$data['applicant_credit_score'] = isset($data['creditscore'])?$data['creditscore']:"Good";
							if($configData['system']['mandrill']['sendToMandrill']==true){
								$mailchimp_response = $objMailChimpNewRetain->saveMemberToList($data);
							}
							
							
							// Retain API Call
							$retainConfigData = (object)$configData['retain_config'];
														
							if($retainConfigData->logging){
								$retain_curl = new Curl();
								$retain_url= $retainConfigData->post_url;
								$retain_data = array();
								$result_data = json_decode(json_encode($result), true);
								$retain_data['TransactionType'] = 'Application';
								$retain_data = $postData;
								$retain_data['DOB'] = isset($variables->birthdate)?stripslashes($variables->birthdate):'';
								$retain_data['applicationId'] = $result->Response->ApplicationID;
								$retain_data['firstName'] = isset($variables->firstname)?$variables->firstname:'';
								$retain_data['lastName'] = isset($variables->lastname)?$variables->lastname:'';
								$retain_data['email'] = isset($variables->email)?$variables->email:'';
								$retain_data['dealerId'] = isset($variables->dealerid)?$variables->dealerid:'';
								$retain_data['uuid'] = isset($variables->uuid)?$variables->uuid:'';
								$retain_data['phonenumber'] = isset($variables->phonenumber)?$variables->phonenumber:'';
								$retain_data['streetaddress'] = isset($variables->streetaddress)?$variables->streetaddress:'';
								$retain_data['appartment'] = isset($variables->appartment)?$variables->appartment:'';
								$retain_data['city'] = isset($variables->city)?$variables->city:'';
								$retain_data['state'] = isset($variables->state)?$variables->state:'';
								$retain_data['zipcode'] = isset($variables->zipcode)?$variables->zipcode:'';
								$retain_data['offerCount'] = $offer_count;
								//$retain_data['api_username'] = $retainConfigData->api_username;
								//$retain_data['api_password'] = $retainConfigData->api_password;
								$retain_data['ssn'] = substr($postData['ssn'],-4);
								$retain_data['SSN'] = substr($postData['SSN'],-4);
								$retain_data['cb_ssn'] = substr($postData['cb_ssn'],-4);
								$retain_data['Response'] = $result_data;
								$method = 'POST';
								$retain_result = $retain_curl->callApi($retain_url, $method,'application/json',$retain_data);
								//echo '<pre>';print_r($retain_result);exit;
							}
							if($configData['system']['mandrill']['sendToMandrill']==true){
								$retain_mail['firstname'] = isset($variables->firstname)?$variables->firstname:'';
								$retain_mail['lastname'] = isset($variables->lastname)?$variables->lastname:'';
								$retain_mail['email'] = isset($variables->email)?$variables->email:'';
								$retain_mail['agreeemail'] = 'true';
								$retain_mail['is_offer'] = 'yes';
								$retain_mail['offerCount'] = $offer_count;
								$retain_mail['dealerid'] = isset($variables->dealerid)?$variables->dealerid:'';
								$retain_mail['loanpurpose'] = isset($variables->loanpurpose)?$variables->loanpurpose:'';
								retain_mailchimp($retain_mail);
							}
						}else{
							// Headway Call
							$head_curl = new Curl();
							$head_curl->logMessages("Headway API and mail-chimp call started.");
							$objMailChimpNew = NEW MailChimp();
							$objMailChimpNew->listId = $configData['system']['mail-chimp']['headway-newsletter-list-id']; // Headway Newsletter - List ID
							$offers = $result->Response->Offers;

							if(isset($variables->email) && $variables->email != ''){
								$_SESSION['borrower_email'] = $variables->email;
								$_SESSION['borrower_firstname'] = $variables->firstname;
							}
							
							// Headway API Call
							$headwayConfigData = (object)$configData['headway_config'];
							$offerCount = 0;
							$offerAmount = array();
							
							for($j = 0; $j<count($result->Response->Offers); $j++){
								if($result->Response->Offers[$j]->LoanAmount >= $variables->loanamount*0.90 && $result->Response->Offers[$j]->displayOffer == "true"){
									$offerCount++;
								}else{
									$offerAmount[] = $result->Response->Offers[$j]->LoanAmount;
								}
								if($result->Response->Offers[$j]->LoanAmount >= $variables->loanamount*0.90 && $result->Response->Offers[$j]->APR > 0){
									$offerApr_count++;
								}
							}
							
							if($offerCount == 0){
								rsort($offerAmount);
								for($k = 0; $k<count($offerAmount); $k++){
									if($offerAmount[$k] == $offerAmount[0]){
										$offerCount++;
									}
								}
							}
							
							$lend_array = array();
							foreach($offers as $offer){
								$lend_array[] = $offer->Lender;
							}
							$lend_array = array_unique($lend_array);
							if($data['email']=='' && isset($result->Response->ApplicationInfo->email) && $result->Response->ApplicationInfo->email !='' ){
								$_SESSION['user_email'] = $result->Response->ApplicationInfo->email;
								$data['email'] = $_SESSION['user_email'];
							}
							$data['offers'] = 'true';
							$data['application_date'] = date('m/d/Y');
							$data['offer_selected_date'] = '';
							// Added AB Test Group logic as per PM-2065
							$test_array = $configData['system']['testGroup'];
							$rand_keys = array_rand($test_array,1);
							$data['testGroup'] = $test_array[$rand_keys];
							$data['application_id'] = $result->Response->ApplicationID;
							$data['cb_indicator'] = $result->Response->cb_indicator;
							$data['number_of_lenders'] = count($lend_array);
							$data['number_of_offers'] = $offerCount;
							
							if($data['creditscore'] == "720-850"){
								$data['applicant_credit_score'] = "Excellent";
							}
							if($data['creditscore'] == "680-719"){
								$data['applicant_credit_score'] = "Good";
							}
							if($data['creditscore'] == "620-679"){
								$data['applicant_credit_score'] = "Average";
							}
							if($data['creditscore'] == "350-619"){
								$data['applicant_credit_score'] = "Poor";
							}
							//$data['applicant_credit_score'] = isset($data['creditscore'])?$data['creditscore']:"Good";
							if($configData['system']['mandrill']['sendToMandrill']==true){
								$mailchimp_response = $objMailChimpNew->saveMemberToList($data);
							}
							
							if($headwayConfigData->logging){
								$headway_url= $headwayConfigData->post_url;
								$result_data = json_decode(json_encode($result), true);
								$headway_data = array();
								$headway_data['TransactionType'] = 'Application';
								$headway_data = $postData;
								$headway_data['DOB'] = isset($variables->birthdate)?stripslashes($variables->birthdate):'';
								$headway_data['applicationId'] = $result->Response->ApplicationID;
								$headway_data['firstName'] = isset($variables->firstname)?$variables->firstname:'';
								$headway_data['lastName'] = isset($variables->lastname)?$variables->lastname:'';
								$headway_data['email'] = isset($variables->email)?$variables->email:'';
								$headway_data['dealerId'] = isset($variables->dealerid)?$variables->dealerid:'';
								$headway_data['uuid'] = isset($variables->uuid)?$variables->uuid:'';
								$headway_data['phonenumber'] = isset($variables->phonenumber)?$variables->phonenumber:'';
								$headway_data['streetaddress'] = isset($variables->streetaddress)?$variables->streetaddress:'';
								$headway_data['appartment'] = isset($variables->appartment)?$variables->appartment:'';
								$headway_data['city'] = isset($variables->city)?$variables->city:'';
								$headway_data['state'] = isset($variables->state)?$variables->state:'';
								$headway_data['zipcode'] = isset($variables->zipcode)?$variables->zipcode:'';
								$headway_data['offerCount'] = $offerCount;
								//$headway_data['api_username'] = $headwayConfigData->api_username;
								//$headway_data['api_password'] = $headwayConfigData->api_password;
								$headway_data['ssn'] = substr($postData['ssn'],-4);
								$headway_data['SSN'] = substr($postData['SSN'],-4);
								$headway_data['cb_ssn'] = substr($postData['cb_ssn'],-4);
								$headway_data['Response'] = $result_data;
								$method = 'POST';
								$headway_result = $head_curl->callApi($headway_url, $method,'application/json',$headway_data);
								$head_curl->logMessages("Headway API call complete.");
								//echo '<pre>';print_r($headway_result);exit;
							}
							if($configData['system']['mandrill']['sendToMandrill']==true){
								$headway_mail['firstname'] = isset($variables->firstname)?$variables->firstname:'';
								$headway_mail['lastname'] = isset($variables->lastname)?$variables->lastname:'';
								$headway_mail['email'] = isset($variables->email)?$variables->email:'';
								$headway_mail['agreeemail'] = 'true';
								$headway_mail['is_offer'] = 'yes';
								$headway_mail['offerCount'] = $offerCount;
								$headway_mail['dealerid'] = isset($variables->dealerid)?$variables->dealerid:'';
								$headway_mail['loanpurpose'] = isset($variables->loanpurpose)?$variables->loanpurpose:'';
								headway_mailchimp($headway_mail);
								$head_curl->logMessages("Headway mail-chimp complete.");
							}
							
							$head_curl->logMessages("Headway API and mail-chimp call ended.");
						}
						if(isset($result->Response->dupCheckResult) && $result->Response->dupCheckResult == "offers available"){
							$_SESSION['isDupCheckOffer'] = 1;
						}

						//print_r($result);exit('call');
						$_SESSION['isOfferAvailable'] = 1;
						$_SESSION[$session_id] = $result;
						$_SESSION[session_id().'-formData'] = $postData;
						$_SESSION['offerAvailableArray'] = array_merge($_SESSION['offerAvailableArray'],array(session_id()));

					}
				}else{

					if(strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
						// Retain API Call
						$variables->loanpurpose = 'Legal Expenses';
						$retainConfigData = (object)$configData['retain_config'];
						if($retainConfigData->logging){
							$retain_curl = new Curl();
							$retain_url= $retainConfigData->post_url;
							$result_data = json_decode(json_encode($result), true);
							$retain_data = array();
							$retain_data = $postData;
							$retain_data['TransactionType'] = 'Application';
							$retain_data = $postData;
							$retain_data['DOB'] = isset($variables->birthdate)?stripslashes($variables->birthdate):'';
							$offerCount = 0;
							$retain_data['TransactionType'] = 'Application';
							$retain_data['applicationId'] = $result->Response->ApplicationID;
							$retain_data['firstName'] = isset($variables->firstname)?$variables->firstname:'';
							$retain_data['lastName'] = isset($variables->lastname)?$variables->lastname:'';
							$retain_data['email'] = isset($variables->email)?$variables->email:'';
							$retain_data['dealerId'] = isset($variables->dealerid)?$variables->dealerid:'';
							$retain_data['uuid'] = isset($variables->uuid)?$variables->uuid:'';
							$retain_data['phonenumber'] = isset($variables->phonenumber)?$variables->phonenumber:'';
							$retain_data['streetaddress'] = isset($variables->streetaddress)?$variables->streetaddress:'';
							$retain_data['appartment'] = isset($variables->appartment)?$variables->appartment:'';
							$retain_data['city'] = isset($variables->city)?$variables->city:'';
							$retain_data['state'] = isset($variables->state)?$variables->state:'';
							$retain_data['zipcode'] = isset($variables->zipcode)?$variables->zipcode:'';
							$retain_data['offerCount'] = '0';
							//$retain_data['api_username'] = $retainConfigData->api_username;
							//$retain_data['api_password'] = $retainConfigData->api_password;
							$retain_data['ssn'] = substr($postData['ssn'],-4);
							$retain_data['SSN'] = substr($postData['SSN'],-4);
							$retain_data['cb_ssn'] = substr($postData['cb_ssn'],-4);
							//$retain_data['TransUnion']['suppressionIndicator'] = ($result_data['Response']['suppressionIndicator'])?$result_data['Response']['suppressionIndicator']:"N";
							$retain_data['Response'] = $result_data;
							$method = 'POST';
							$retain_result = $retain_curl->callApi($retain_url, $method,'application/json',$retain_data);
							//echo '<pre>';print_r($retain_result);exit;
						}
						$objMailChimpNewRetain = NEW MailChimp();
						$objMailChimpNewRetain->listId = $configData['system']['mail-chimp']['retain-newsletter-list-id']; // Retain Newsletter - List ID
						$data['offers'] = 'false';
						$data['application_date'] = date('m/d/Y');
						$data['offer_selected_date'] = '';
						// Added AB Test Group logic as per PM-2065
						$test_array = $configData['system']['testGroup'];
						$rand_keys = array_rand($test_array,1);
						$data['testGroup'] = $test_array[$rand_keys];
						$data['application_id'] = $result->Response->ApplicationID;
						$data['cb_indicator'] = $result->Response->cb_indicator;
						if($data['creditscore'] == "720-850"){
							$data['applicant_credit_score'] = "Excellent";
						}
						if($data['creditscore'] == "680-719"){
							$data['applicant_credit_score'] = "Good";
						}
						if($data['creditscore'] == "620-679"){
							$data['applicant_credit_score'] = "Average";
						}
						if($data['creditscore'] == "350-619"){
							$data['applicant_credit_score'] = "Poor";
						}
						if($configData['system']['mandrill']['sendToMandrill']==true){
							$mailchimp_response = $objMailChimpNewRetain->saveMemberToList($data);
							$retain_mail['firstname'] = isset($variables->firstname)?$variables->firstname:'';
							$retain_mail['lastname'] = isset($variables->lastname)?$variables->lastname:'';
							$retain_mail['email'] = isset($variables->email)?$variables->email:'';
							$retain_mail['agreeemail'] = 'true';
							$retain_mail['is_offer'] = 'no';
							$retain_mail['dealerid'] = isset($variables->dealerid)?$variables->dealerid:'';
							$retain_mail['loanpurpose'] = isset($variables->loanpurpose)?$variables->loanpurpose:'';
							retain_mailchimp($retain_mail);
						}
					}else{
						// Headway API Call
						$head_curl = new Curl();
						$head_curl->logMessages("Headway no-offers API and mail-chimp call started.");
						$headwayConfigData = (object)$configData['headway_config'];
						if($headwayConfigData->logging){
							$headway_url= $headwayConfigData->post_url;
							$result_data = json_decode(json_encode($result), true);
							$headway_data = array();
							$headway_data = $postData;
							$headway_data['DOB'] = isset($variables->birthdate)?stripslashes($variables->birthdate):'';
							$offerCount = 0;
							$headway_data['TransactionType'] = 'Application';
							$headway_data['applicationId'] = $result->Response->ApplicationID;
							$headway_data['firstName'] = isset($variables->firstname)?$variables->firstname:'';
							$headway_data['lastName'] = isset($variables->lastname)?$variables->lastname:'';
							$headway_data['email'] = isset($variables->email)?$variables->email:'';
							$headway_data['dealerId'] = isset($variables->dealerid)?$variables->dealerid:'';
							$headway_data['uuid'] = isset($variables->uuid)?$variables->uuid:'';
							$headway_data['phonenumber'] = isset($variables->phonenumber)?$variables->phonenumber:'';
							$headway_data['streetaddress'] = isset($variables->streetaddress)?$variables->streetaddress:'';
							$headway_data['appartment'] = isset($variables->appartment)?$variables->appartment:'';
							$headway_data['city'] = isset($variables->city)?$variables->city:'';
							$headway_data['state'] = isset($variables->state)?$variables->state:'';
							$headway_data['zipcode'] = isset($variables->zipcode)?$variables->zipcode:'';
							$headway_data['offerCount'] = '0';
							//$headway_data['api_username'] = $headwayConfigData->api_username;
							//$headway_data['api_password'] = $headwayConfigData->api_password;
							$headway_data['ssn'] = substr($postData['ssn'],-4);
							$headway_data['SSN'] = substr($postData['SSN'],-4);
							$headway_data['cb_ssn'] = substr($postData['cb_ssn'],-4);
							//$headway_data['TransUnion']['suppressionIndicator'] = ($result_data['Response']['suppressionIndicator'])?$result_data['Response']['suppressionIndicator']:"N";
							$headway_data['Response'] = $result_data;
							$method = 'POST';
							$headway_result = $head_curl->callApi($headway_url, $method,'application/json',$headway_data);
							//echo '<pre>';print_r($headway_result);exit;
							$head_curl->logMessages("Headway no-offers API call complete.");
						}

						// Added this logic for loanAppOrgNoOffersType GTM Layer field
						$_SESSION['application_outcome'] = '';
						if(isset($result->Response->TransUnion->suppressionIndicator) && $result->Response->TransUnion->suppressionIndicator != 'N'){
							$_SESSION['application_outcome'] = 'freeze';
						}else if(isset($result->Response->TransUnion->TUFileHit) && $result->Response->TransUnion->TUFileHit != 'Y'){
							$_SESSION['application_outcome'] = 'no hit';
						}else if(isset($result->Response->DuplicateApps->tooManyApps21Days) && $result->Response->DuplicateApps->tooManyApps21Days == 'true'){
							$_SESSION['application_outcome'] = 'too many apps';
						}else {
							$_SESSION['application_outcome'] = 'no offers';
						}
						$objMailChimpNew = NEW MailChimp();
						$objMailChimpNew->listId = $configData['system']['mail-chimp']['headway-newsletter-list-id']; // Headway Newsletter - List ID
						$data['offers'] = 'false';
						$data['application_date'] = date('m/d/Y');
						$data['offer_selected_date'] = '';
						// Added AB Test Group logic as per PM-2065
						$test_array = $configData['system']['testGroup'];
						$rand_keys = array_rand($test_array,1);
						$data['testGroup'] = $test_array[$rand_keys];
						$data['application_id'] = $result->Response->ApplicationID;
						$data['cb_indicator'] = $result->Response->cb_indicator;

						$to = isset($variables->email)?$variables->email:'';
						$subject = 'The subject';
						$body = 'The email body content';
						$headers = array('Content-Type: text/html; charset=UTF-8');

						//wp_mail( $to, $subject, $body, $headers );

						if($data['creditscore'] == "720-850"){
							$data['applicant_credit_score'] = "Excellent";
						}
						if($data['creditscore'] == "680-719"){
							$data['applicant_credit_score'] = "Good";
						}
						if($data['creditscore'] == "620-679"){
							$data['applicant_credit_score'] = "Average";
						}
						if($data['creditscore'] == "350-619"){
							$data['applicant_credit_score'] = "Poor";
						}
						if($configData['system']['mandrill']['sendToMandrill']==true){
							$mailchimp_response = $objMailChimpNew->saveMemberToList($data);
							$headway_mail['firstname'] = isset($variables->firstname)?$variables->firstname:'';
							$headway_mail['lastname'] = isset($variables->lastname)?$variables->lastname:'';
							$headway_mail['email'] = isset($variables->email)?$variables->email:'';
							$headway_mail['agreeemail'] = 'true';
							$headway_mail['is_offer'] = 'no';
							$headway_mail['dealerid'] = isset($variables->dealerid)?$variables->dealerid:'';
							$headway_mail['loanpurpose'] = isset($variables->loanpurpose)?$variables->loanpurpose:'';
							$headway_mail['suppressionIndicator'] = isset($result->Response->TransUnion->suppressionIndicator)?$result->Response->TransUnion->suppressionIndicator:'N';
							//$headway_mail['suppressionIndicator'] = 'F';

							headway_mailchimp($headway_mail);
							$head_curl->logMessages("Headway no-offers mail-chimp call complete.");
						}
						$head_curl->logMessages("Headway no-offers API and mail-chimp call ended.");
					}
					
					$_SESSION['isOfferAvailable'] = 0;
				}
			}

			// Added below line for offer version testing
			//$result->Response->{'offerVersion'} = 'ov-0002';
			if(strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
				$headwayOfferVersions = $configData['retain_config']['offerVersions'];
			}else{
				$headwayOfferVersions = $configData['headway_config']['offerVersions'];
			}
			//$headwayOfferVersions = $configData['headway_config']['offerVersions'];
			//print_r($result->Response->offersVersion);
			//if($result->Response->offersVersion == 'ov-0002'){
			foreach($headwayOfferVersions as $headwayOfferVersion){
				if($result->Response->offersVersion != '' && $headwayOfferVersion['version'] == $result->Response->offersVersion){
					$buildNumber = $headwayOfferVersion['files']['build'];
					$buildCSS = $headwayOfferVersion['files']['css'];
				}else if($headwayOfferVersion['version'] == $configData['defaultVersion']){
					$buildNumber = $headwayOfferVersion['files']['build'];
					$buildCSS = $headwayOfferVersion['files']['css'];
				}
			}

			if($offerApr_count === 0 && !isset($_GET['application_id'])){
				foreach($headwayOfferVersions as $headwayOfferVersion){
					if($headwayOfferVersion['version'] == $configData['defaultVersion']){
						$buildNumber = $headwayOfferVersion['files']['build'];
						$buildCSS = $headwayOfferVersion['files']['css'];
					}
				}
			}
			//echo '<pre>';print_r($result);
		} 
	}
	
	//Handle Intercom Self-Reported Credit == Poor
	if ($postData['affiliateid'] == "intercom" && $postData['creditscore'] == "350-619"){
		wp_redirect($PLconfigData['intercomRedirectUrl']);
		exit;
	}
	
	// Setting Timezone to Pacific Timezone
	date_default_timezone_set('America/Los_Angeles');
	// Fetching and checking the Accredited Debt Relief count and time
	$accredited_count = get_field('accredited_debt_relief_count','option');
	$accredited_last_call = get_field('accredited_debt_relief_last_call_date','option');
	$accredited_status = 'true';
	// Check for the capacity limit for Accredited and update according to logic
	if($accredited_count == 100){
		$accredited_last_call_month = date('Y-m', strtotime($accredited_last_call));
		$accredited_current_month = date('Y-m', strtotime("now"));
		
		if($accredited_last_call_month == $accredited_current_month){
			// Update the flag to false if limit is reached and last call date has the same current month
			$accredited_status = 'false';
		}else{
			// Reset counter if the last call month is different than current month
			update_field('accredited_debt_relief_count',0,'option');
		}
	}
	
	
	// Fetching and checking the Freedom Debt Relief count and time
	$freedom_count = get_field('freedom_debt_relief_count','option');
	$freedom_last_call = get_field('freedom_debt_relief_last_call_date','option');
	$freedom_status = 'true';
	// Check for the capacity limit for Freedom and update according to logic
	if($freedom_count == 40){
		$freedom_last_call_day = date('Y-m-d', strtotime($accredited_last_call));
		$freedom_current_day = date('Y-m-d', strtotime("now"));
		
		if($freedom_last_call_day == $freedom_current_day){
			// Update the flag to false if limit is reached and last call date has the current date
			$freedom_status = 'false';
		}else{
			// Reset counter if the last call day is different than current day
			update_field('freedom_debt_relief_count',0,'option');
		}
	}
	
	if($result->Response->TransactionType == "leadCheckApp"){
		if(isset($result->Response->ApplicationInfo->firstname) && $result->Response->ApplicationInfo->firstname != ''){
			$variables->firstname = $result->Response->ApplicationInfo->firstname;
		}
		if(isset($result->Response->ApplicationInfo->loanamount) && $result->Response->ApplicationInfo->loanamount != ''){
			$variables->loanamount = $result->Response->ApplicationInfo->loanamount;
		}
		if(isset($result->Response->fs)){
			if($result->Response->fs == '1' || $result->Response->fs == '2' || $result->Response->fs == '3'){
				$variables->creditscore = '720-850';
			}
			if($result->Response->fs == '4'){
				$variables->creditscore = '680-719';
			}
			if($result->Response->fs == '5'){
				$variables->creditscore = '620-679';
			}
			if($result->Response->fs == '6' || $result->Response->fs == '7' || $result->Response->fs == '8' || $result->Response->fs == ''){
				$variables->creditscore = '350-619';
			}
		}
	}
	if(isset($result->Response->ApplicationID) && $result->Response->ApplicationID != ''){
		$ApplicationID = $result->Response->ApplicationID;
	}else{
		$ApplicationID = undefined;
	}
	if(isset($result->Response->uuid) && $result->Response->uuid != ''){
		$uuid = $result->Response->uuid;
	}else{
		$uuid = undefined;
	}

	//exit('after leadcheck2');
	?><script>
		window.hasErrorInWebservice = "<?php echo ($result->http_code==200)?'false':'true'; ?>"; 
		//console.log(window.hasErrorInWebservice);
		window.lenderResponse = <?php echo json_encode($result); ?>;
		window.isPR = <?php if(strpos($_SERVER["HTTP_HOST"],"dev.primerates.com") !== false || strpos($_SERVER["HTTP_HOST"],"qa.primerates.com") !== false || strpos($_SERVER["HTTP_HOST"],"www.primerates.com") !== false || strpos($_SERVER['HTTP_HOST'],'qa-coopertiva.api-dataview360.com') !== false){ echo "1"; }else{ echo "0"; } ?>;
		window.isHeadway = <?php if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){ echo "1"; }else{ echo "0"; } ?>;
		<?php if($postData['TransactionType'] != 'reusable_offers_check'){  // For PM-1902 ?>
		window.isOfferAvailable = <?php echo $_SESSION['isOfferAvailable']; ?>;

		// Setting Cookie variables for No Offers Page PM-1067  --Start
		if(window.isOfferAvailable == "0"){
			var d = new Date();
			d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));
			var expires = "expires="+d.toUTCString();

			document.cookie = "FirstName=<?php echo isset($variables->firstname)?$variables->firstname:''; ?>" + ";expires=0;path=/";
			document.cookie = "LastName=<?php echo isset($variables->lastname)?$variables->lastname:''; ?>" + ";expires=0;path=/";
			document.cookie = "DealerID=<?php echo isset($variables->dealerid)?$variables->dealerid:'undefined'; ?>" + ";expires=0;path=/";
			document.cookie = "AppVersion=<?php echo isset($variables->appVersion)?$variables->appVersion:undefined; ?>" + ";expires=0;path=/";
			document.cookie = "Outcome=<?php echo isset($result->Response->outcome)?$result->Response->outcome:undefined; ?>" + ";expires=0;path=/";
			document.cookie = "offersVersion=<?php echo isset($result->Response->offersVersion)?$result->Response->offersVersion:undefined; ?>" + ";expires=0;path=/";
			document.cookie = "ApplicationID=<?php echo $ApplicationID; ?>" + ";expires=0;path=/";
			document.cookie = "UUID=<?php echo $uuid; ?>" + ";expires=0;path=/";
			document.cookie = "Email=<?php echo isset($variables->email)?$variables->email:''; ?>" + ";expires=0;path=/";
			document.cookie = "Phone=<?php echo isset($variables->phonenumber)?$variables->phonenumber:''; ?>" + ";expires=0;path=/";
			document.cookie = "IPAddress=<?php echo $_SERVER['REMOTE_ADDR']; ?>" + ";expires=0;path=/";
			document.cookie = "AnnualIncome=<?php echo isset($variables->annualincome)?$variables->annualincome:''; ?>" + ";expires=0;path=/";
			document.cookie = "LoanAmount=<?php echo isset($variables->loanamount)?$variables->loanamount:''; ?>" + ";expires=0;path=/";
			document.cookie = "State=<?php echo isset($variables->state)?$variables->state:''; ?>" + ";expires=0;path=/";
			document.cookie = "Housing=<?php echo isset($variables->housing)?$variables->housing:''; ?>" + ";expires=0;path=/";
			document.cookie = "CreditScore=<?php echo isset($variables->creditscore)?$variables->creditscore:''; ?>" + ";expires=0;path=/";
			document.cookie = "AgreeTCPA=<?php echo isset($variables->agreeTCPA)?$variables->agreeTCPA:''; ?>" + ";expires=0;path=/";
			document.cookie = "TotalDebt=<?php echo isset($result->Response->TR05091)?$result->Response->TR05091:''; ?>" + ";expires=0;path=/";
			document.cookie = "suppressionIndicator=<?php echo isset($result->Response->TransUnion->suppressionIndicator)?$result->Response->TransUnion->suppressionIndicator:'N'; ?>" + ";expires=0;path=/";
			document.cookie = "AccreditedStatus=<?php echo $accredited_status; ?>" + ";expires=0;path=/";
			document.cookie = "FreedomStatus=<?php echo $freedom_status; ?>" + ";expires=0;path=/";
			document.cookie = "AffiliateID=<?php echo isset($variables->affiliateid)?$variables->affiliateid:''; ?>" + ";expires=0;path=/";
			document.cookie = "LenderResponse=" + JSON.stringify(window.lenderResponse) + ";expires=0;path=/";
		}
		// Setting Cookie variables for No Offers Page PM-1067  --End
		<?php } ?>
		if(window.isHeadway == "1"){
			//var noOfferPage = "<?php echo get_site_url(); ?>/no-offers";
			// For PM-1920
			var noOfferPage = "<?php echo get_site_url(); ?>/no-offers?<?php echo isset($variables->utm_campaign)?'utm_campaign='.$variables->utm_campaign:''; ?>&state=<?php echo isset($variables->state)?$variables->state:''; ?>";
			//console.log('headway');
			//console.log(noOfferPage);
		}else{
			//console.log('non headway');
			//var noOfferPage = "<?php echo get_site_url(); ?>/personal-loans/<?php echo $parentPage; ?>/no-offers";
			var noOfferPage = "<?php echo $noOfferURL; ?>";
			//console.log(noOfferPage);
		}
		if ('Response' in window.lenderResponse) {
			//console.log('Response If');
			if ('Offers' in window.lenderResponse.Response) {
				//console.log('Offers If');
				if(window.lenderResponse.Response.Offers.length==0){
					//console.log('Offers length 0');
					window.location.href = noOfferPage;
				}else{
					if(window.isOfferAvailable == "0"){
						window.location.href = noOfferPage;
					}else{
						if(window.lenderResponse.Response.TransactionType != "reusable_offers_check"){
							//console.log('Offers length 3');
							if(!window.hasErrorInWebservice){
								if(lenderResponse.Response != null && lenderResponse.Response.DeclineReason){
									window.hasErrorInWebservice = true;
								}
							}
							window.isDupCheckOffer = '<?php echo isset($_SESSION['isDupCheckOffer'])?$_SESSION['isDupCheckOffer']:''; ?>';
							window.fName = '<?php echo isset($variables->firstname)?$variables->firstname:''; ?>';
							window.lName = '<?php echo isset($variables->lastname)?$variables->lastname:''; ?>';
							window.loanAmount = '<?php echo isset($variables->loanamount)?$variables->loanamount:''; ?>';
							window.partnerId = '<?php echo isset($variables->partnerid)?$variables->partnerid:''; ?>';
							window.featured = '<?php echo isset($variables->featured)?$variables->featured:''; ?>';
							window.loanPurpose = '<?php echo isset($variables->loanpurpose)?$variables->loanpurpose:''; ?>';
							if(window.isPR == "1" || window.isHeadway == "1"){
								window.hasCoBorrower = '<?php if(isset($variables->cb_indicator) && $variables->cb_indicator == 'true'){ echo 'true';}else{echo 'false';} ?>';
							}
						}else if(window.lenderResponse.Response.TransactionType == "reusable_offers_check"){
							//console.log('Test');
							if(window.isPR == "1" || window.isHeadway == "1"){
								if(window.lenderResponse.Response.cb_indicator == "true"){
									window.hasCoBorrower = 'true';
								}else{
									window.hasCoBorrower = 'false';
								}
							}
						}else{
							//console.log('Offers length 5');
						}
						window.userEmail = '<?php echo isset($variables->email)?$variables->email:''; ?>';
					}
				}
				
				
			}else{
				//console.log('redirect no Offer 1');
				window.location.href = noOfferPage;
			}
		}else{
			//console.log('redirect no Offer 2');
			window.location.href = noOfferPage;
		}
		</script>
		
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/range-slider.css" rel="stylesheet">
		
		<?php if(strpos($_SERVER['HTTP_HOST'],'apply.headwaysales.com') !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"codepen.io") !== false || strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){ ?>
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/lenderoffers-headway-style-base.css" rel="stylesheet">
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/<?php echo $buildCSS; ?>" rel="stylesheet">
		<?php }
		// Bank specific css loading
		else if(isset($versionConfigData['domains']) && !empty($versionConfigData['domains'])){
			foreach($versionConfigData['domains'] as $host){
				if(isset($host['hosts']) && is_array($host['hosts'])){
					foreach($host['hosts'] as $host_url){
						if($_SERVER['HTTP_HOST'] == $host_url['host']){ ?>
				<link href="<?php echo get_stylesheet_directory_uri() ?>/css/<?php echo $host['offer_css']; ?>" rel="stylesheet">
				<link href="<?php echo get_stylesheet_directory_uri() ?>/css/<?php echo $host['lenderoffer_css']; ?>" rel="stylesheet">
				<?php }
					}
				}
			}
		}else{ ?>
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/offer-style.css" rel="stylesheet">
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/lenderoffers-style.css" rel="stylesheet">
		<?php } ?>

<?php }

function prime_rates_offers_page_shortcodes_init(){
	add_shortcode('pre-qualified-offers', 'pre_qualified_offers_shortcode');
}

add_action( 'init', 'prime_rates_offers_page_shortcodes_init' );
