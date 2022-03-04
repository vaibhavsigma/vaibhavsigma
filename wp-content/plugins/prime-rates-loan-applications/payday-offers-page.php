<?php
define( 'PRIME_RATES_PAYDAY_OFFERS_PAGE_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'payday-offers'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_PAYDAY_OFFERS_PAGE_CSS_URL', PRIME_RATES_PAYDAY_OFFERS_PAGE_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_PAYDAY_OFFERS_PAGE_JS_URL', PRIME_RATES_PAYDAY_OFFERS_PAGE_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );

function pre_qualified_payday_offers_shortcode(){
	$html = '<div class="main-cont" id="root"></div>';
	
	if(isset($_SESSION['applicationid'])){
		$html .= '<script>window.disableClaimOffer=true;</script>';
	}
	if($_POST){
		if(isset($_SESSION['multi_step_data'])){
			$prevData = $_SESSION['multi_step_data'];
			$allData = array_merge($prevData,$_POST);
			makeRequestToDV360PD('pre-qualify',$allData);
			unset($_SESSION['multi_step_data']);
		}else{
			makeRequestToDV360PD('pre-qualify',$_POST);
		}

	}else{
		if(isset($_SESSION[session_id().'-formData']) && !empty($_SESSION[session_id().'-formData'])){
			makeRequestToDV360PD('pre-qualify',$_SESSION[session_id().'-formData']);
		}else{
			wp_redirect( get_site_url() );
			exit;
		}
	} return $html;
	 
}
function makeRequestToDV360PD($parentPage,$postData){
	global $configData,$PLconfigData,$versionConfigData,$partner_theme_config;
	
	if(!session_id()){session_start();}
	$hSessionID= session_id();
	$_SESSION['isPayday'] = 1;
	// Check to see if its multipage request and session variables available if not available than redirect to 1st page of multi-page form
	if(isset($postData['action']) && $postData['action'] == 'multi_step_form_submit' && !isset($postData['loanamount'])){
		echo '<script>alert("Sorry, your session has expired. Please complete the loan application again.");</script>';
		$multi_url = get_site_url(). '/personal-loans/app-form-start';
		wp_redirect( $multi_url );
		exit;
	}
	
	
	$isOfferAvailable = false;
    $_SESSION['isOfferAvailable'] = $isOfferAvailable;
	
	$userSessionId = session_id();
    if (!isset($_SESSION[session_id().'-formData'])){
        $_SESSION[session_id().'-formData'] = $postData;
    }else{
		// Set condition for reusable offers to use new form post data every time
		if(isset($postData['TransactionType']) && $postData['TransactionType'] == 'reusable_offers_check'){
			$postData =  $postData;
		}else{
			$postData =  $_SESSION[session_id().'-formData'];
		}
    }
    $session_id = @$postData['uuid'].'-'.$userSessionId;
	
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
	
	// Offer not set into session then call the dv360 otherwise else
    if (!$isOfferAvailable || isset($_GET['applicationid'])){ // Added additional condition for Headway dealers to lookup another application id withing same user session - PM-730 - 08-08-2018
		if(isset($configData['dv360'])){
		// echo "<pre>";print_r($configData);exit;
		// echo "<pre>";print_r($configData);
		// echo "<br/>";print_r($configData['dv360']);echo $configData['dv360']['post_url'];echo "<br/>";echo "Test-URL";echo "<br/>";
		// echo "</pre>";
			$dvConfigData = (object)$configData['dv360'];
			$curl = new Curl();
			//$url=$dvConfigData->mock_url;
			$url="https://".$_SERVER["HTTP_HOST"]."/DV360MockNew.php";
			$method = 'POST';
			$data = $postData;
			if(isset($data['TransactionType']) && $data['TransactionType'] == "reusable_offers_check"){
				if(!isset($_SESSION['accessOffersCount']) && empty($_SESSION['accessOffersCount'])){
					$offer_access_cnt = 0;
					$_SESSION['accessOffersCount'] = $offer_access_cnt;
				}else{
					if($_SESSION['accessOffersCount'] == 3){
						$_SESSION['accessOffersCount'] = 0;
					}
				}
				$data['TransactionType'] = 'reusable_offers_check';
			}else if(isset($data['TransactionType']) && $data['TransactionType'] == "leadCheckApp"){
				$data['TransactionType'] = 'leadCheckApp';
				unset($data['loanPurpose']);
				unset($data['loanpurpose']);
			}else{
				if(strpos($_SERVER["HTTP_HOST"],"qa-cashmax.api-dataview360.com") !== false){
					$data['TransactionType'] = 'PaydayApplication';
				}else{
					$data['TransactionType'] = 'PaydayApplicationGDS';
				}
			}
			
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
				
				$result = $curl->callApi($url, $method,'application/json',array('Request'=>$data));
				if(isset($result->Response->Offers) && !empty($result->Response->Offers) && strtolower($result->Response->outcome) == "offers available"){
					
					// Delete offer URL for application id request
					if(isset($_SESSION['applicationid'])){
						for($i = 0; $i < count($result->Response->Offers); $i++){
							$result->Response->Offers[$i]->OfferURL = "";
						}
					}
					$_SESSION['isOfferAvailable'] = 1;
					$_SESSION[$session_id] = $result;
					$_SESSION[session_id().'-formData'] = $postData;
					$_SESSION['offerAvailableArray'] = array_merge($_SESSION['offerAvailableArray'],array(session_id()));
					
				}else if(isset($result->Response->outcome) && strtolower($result->Response->outcome) == "application expired"){
					$_SESSION['isOfferAvailable'] = 0;
					$_SESSION['expired_result'] = json_encode($result);
					$_SESSION['dealerid'] = isset($data['dealerid'])?$data['dealerid']:'';
					if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"loans.zalea.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){
						$access_url = get_site_url(). '/access-offers-expired';
					}else{
						$access_url = get_site_url(). '/personal-loans/access-offers-expired';
					}
					wp_redirect( $access_url );
					exit;
				}else if(isset($result->Response->outcome) && strtolower($result->Response->outcome) == "no offers available"){
					$_SESSION['isOfferAvailable'] = 0;
					$_SESSION['no-offers-ApplicationDate'] = $result->Response->ApplicationInfo->ApplicationDate;
					$_SESSION['no-offers-loanpurpose'] = $result->Response->ApplicationInfo->loanpurpose;
					$_SESSION['no-offers-loanamount'] = $result->Response->ApplicationInfo->loanamount;
					$_SESSION['no-offers-applicationid'] = $result->Response->ApplicationInfo->ApplicationID;
					if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"loans.zalea.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){
						$access_url = get_site_url(). '/access-offers-no-offers';
					}else{
						$access_url = get_site_url(). '/personal-loans/access-offers-no-offers';
					}
					wp_redirect( $access_url );
					exit;
				}else{
					if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"loans.zalea.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){
						$_SESSION['isOfferAvailable'] = 0;
						$_SESSION['accessOffersCount']++;
						$access_url = get_site_url(). '/access-offers';
						wp_redirect( $access_url );
						exit;
					}else{
						$_SESSION['isOfferAvailable'] = 0;
						$_SESSION['accessOffersCount']++;
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
				if(strpos($_SERVER['HTTP_HOST'],'qa-cashmax.api-dataview360.com') !== false){
					$data['TransactionType'] = "PaydayApplication";
				}else if(strpos($_SERVER['HTTP_HOST'],'qa-incredible.api-dataview360.com') !== false){
					$data['TransactionType'] = "PaydayIncredibleOffers";
				}else{
					$data['TransactionType'] = "PaydayApplicationGDS";
				}
				//echo '<pre><script>alert(window.loanPurpose);</script>';print_r($data);exit;
				$result = $curl->callApi($url, $method,'application/json',array('Request'=>$data));
				//print_r($result);exit;
				
				if($result->http_code!=200){
					$cnt = 1;
					do {
						$result = $curl->callApi($url, $method,'application/json',array('Request'=>$data));
						$cnt++;
					} while ( $cnt <= $dvConfigData->attempt_cnt_on_error && $result->http_code != 200 );
				}
				//print_r($result->Response);exit('call');
				if (isset($result) && !empty($result->Response->Offers)){
					if(isset($result->Response->dupCheckResult) && $result->Response->dupCheckResult == "no offers available"){
						$_SESSION['no-offers-ApplicationDate'] = $result->Response->most_recent_application_datetime;
						$_SESSION['no-offers-loanpurpose'] = $result->Response->most_recent_application_loan_purpose;
						$_SESSION['no-offers-loanamount'] = $result->Response->most_recent_application_loan_amount;
						$_SESSION['no-offers-applicationid'] = $result->Response->lastApplicationID;
						$_SESSION['isOfferAvailable'] = 0;
						
					}else{
						// Added this logic for GDS Parter theme with borrower email logic 
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
									}else{
										if(isset($variables->email) && $variables->email != ''){
											$_SESSION['borrower_email'] = $variables->email;
										}
										$_SESSION['loanamount'] = $variables->loanamount;
										$_SESSION['loanpurpose'] = $variables->loanpurpose;
										$_SESSION['firstname'] = $variables->firstname;
										$_SESSION['lastname'] = $variables->lastname;
									}
								}
							}
							//print_r($partner_theme_config);exit;
						}		
						if(isset($result->Response->dupCheckResult) && $result->Response->dupCheckResult == "offers available"){
							$_SESSION['isDupCheckOffer'] = 1;
						}
						$_SESSION['isOfferAvailable'] = 1;
						$_SESSION[$session_id] = $result;
						$_SESSION[session_id().'-formData'] = $postData;
						$_SESSION['offerAvailableArray'] = array_merge($_SESSION['offerAvailableArray'],array(session_id()));
					}
				}else{
					$_SESSION['isOfferAvailable'] = 0;
				}
			}
			//echo '<pre>';print_r($result);
		} 
	}
	
	
	?>
		 
		<script>
		window.hasErrorInWebservice = "<?php echo ($result->http_code==200)?'false':'true'; ?>"; 
		//console.log(window.hasErrorInWebservice);
		window.lenderResponse = <?php echo json_encode($result); ?>;
		window.isPR = <?php if(strpos($_SERVER["HTTP_HOST"],"dev.primerates.com") !== false || strpos($_SERVER["HTTP_HOST"],"qa.primerates.com") !== false || strpos($_SERVER["HTTP_HOST"],"www.primerates.com") !== false){ echo "1"; }else{ echo "0"; } ?>;
		window.isHeadway = <?php if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){ echo "1"; }else{ echo "0"; } ?>;
		window.isZalea = <?php if(strpos($_SERVER["HTTP_HOST"],"zalea.com") !== false){ echo "1"; }else{ echo "0"; } ?>;
		window.isOfferAvailable = <?php echo $_SESSION['isOfferAvailable']; ?>;
		
		// Setting Cookie variables for No Offers Page PM-1067  --Start
		if(window.isOfferAvailable == "0"){
			var d = new Date();
			d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));
			var expires = "expires="+d.toUTCString();
			
			document.cookie = "FirstName=<?php echo isset($variables->firstname)?$variables->firstname:''; ?>" + ";expires=0;path=/";
			document.cookie = "LastName=<?php echo isset($variables->lastname)?$variables->lastname:''; ?>" + ";expires=0;path=/";
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
			document.cookie = "AccreditedStatus=<?php echo $accredited_status; ?>" + ";expires=0;path=/";
			document.cookie = "FreedomStatus=<?php echo $freedom_status; ?>" + ";expires=0;path=/";
			document.cookie = "AffiliateID=<?php echo isset($variables->affiliateid)?$variables->affiliateid:''; ?>" + ";expires=0;path=/";
			document.cookie = "LenderResponse=" + JSON.stringify(window.lenderResponse) + ";expires=0;path=/";
		}
		// Setting Cookie variables for No Offers Page PM-1067  --End
		
		if(window.isHeadway == "1" || window.isZalea == "1"){
			var noOfferPage = "<?php echo get_site_url(); ?>/no-offers";
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
		<link href="<?php echo PRIME_RATES_PAYDAY_OFFERS_PAGE_CSS_URL; ?>main.097b838a.css" rel="stylesheet">
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/range-slider.css" rel="stylesheet">
		<?php if(strpos($_SERVER['HTTP_HOST'],'loans.zalea.com') !== false ){ ?>
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/offer-zalea-style.css" rel="stylesheet">
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/lenderoffers-zalea-style.css" rel="stylesheet">
		<?php }else if(strpos($_SERVER['HTTP_HOST'],'apply.headwaysales.com') !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false){ ?>
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/offer-headway-style.css" rel="stylesheet">
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/lenderoffers-headway-style.css" rel="stylesheet">
		<?php }
		// Bank specific css loading
		else if(isset($partner_theme_config) && is_array($partner_theme_config['hosts'])){
			//print_r($partner_theme_config);exit;
			foreach($partner_theme_config['hosts'] as $host_url){
				if($_SERVER['HTTP_HOST'] == $host_url['host'] && ($_SERVER['HTTP_HOST'] == 'qa.api-dataview360.com' || $_SERVER['HTTP_HOST'] == 'qa-incredible.api-dataview360.com')){ ?>
				<link href="<?php echo get_stylesheet_directory_uri() ?>/css/<?php echo $partner_theme_config['payday_offer_css']; ?>" rel="stylesheet">
				<link href="<?php echo get_stylesheet_directory_uri() ?>/css/<?php echo $partner_theme_config['payday_lenderoffer_css']; ?>" rel="stylesheet">
				<?php }else{ ?>
				<link href="<?php echo get_stylesheet_directory_uri() ?>/css/<?php echo $partner_theme_config['offer_css']; ?>" rel="stylesheet">
				<link href="<?php echo get_stylesheet_directory_uri() ?>/css/<?php echo $partner_theme_config['lenderoffer_css']; ?>" rel="stylesheet">
				<?php }
			}
		}else{ ?>
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/offer-style.css" rel="stylesheet">
		<link href="<?php echo get_stylesheet_directory_uri() ?>/css/lenderoffers-style.css" rel="stylesheet">
		<?php } ?>
<?php }

function payday_offers_page_enqueue() {
	global $post;
	if(has_shortcode( $post->post_content, 'payday-offers') && ($_POST || (isset($_SESSION['isOfferAvailable']) && $_SESSION['isOfferAvailable'] == 1))){
		$action = 'mandrill_incomplete';
		$_SESSION['isPayday'] = '1';
	  wp_enqueue_script('prime_rates_loan_applications', PRIME_RATES_PAYDAY_OFFERS_PAGE_JS_URL.'main.d5f1eccd.js', array(), 0,false );  
	} 
}

function payday_offers_page_shortcodes_init(){
	add_shortcode('payday-offers', 'pre_qualified_payday_offers_shortcode');
}


add_action('init', 'payday_offers_page_shortcodes_init');
add_action( 'wp_enqueue_scripts', 'payday_offers_page_enqueue' );
