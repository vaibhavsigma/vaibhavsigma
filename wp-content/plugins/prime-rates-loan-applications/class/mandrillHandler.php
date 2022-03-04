<?php
require_once 'mandrill-api-php/src/Mandrill.php'; //Not required with Composer
require_once 'mailChimp.php';
class MandrillHandler{
	public $objMandrill = [];
	private $mandrillConfigData=[];
	private $urlForErrorEmail = 'Mandrill :';
	public function __construct() {
	   global $configData;
	   if(strpos($_SERVER['HTTP_HOST'],'kn.pr.acornfinance.com') !== false || strpos($_SERVER['HTTP_HOST'],'mykukun.com') !== false){
		   $this->mandrillConfigData = (object)$configData['kukun_config']['mandrill'];
	   }else if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER['HTTP_HOST'],'pr.acornfinance.com') !== false){
		   $this->mandrillConfigData = (object)$configData['headway_config']['mandrill'];
	   }else if((isset($_POST["isZalea"]) && $_POST["isZalea"] == "1") || strpos($_SERVER["HTTP_HOST"],"zalea.com") !== false){
		   $this->mandrillConfigData = (object)$configData['zalea_config']['mandrill'];
	   }else if(strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
		   $this->mandrillConfigData = (object)$configData['retain_config']['mandrill'];
	   }else{
		   $this->mandrillConfigData = (object)$configData['system']['mandrill'];
	   }
	   $this->objMandrill = new Mandrill($this->mandrillConfigData->api_key);
    }
	public function scheduleAbandonedEmail($data){
		try{
			$mndSuffix = $_SESSION['mndSuffix'];
			$abanDonedEmailConfig = (object)$this->mandrillConfigData->abandoned_mail;
			$data['subject'] = $abanDonedEmailConfig->subject;
			if(!isset($_SESSION['mndSuffix']) && (strpos($_SERVER['HTTP_HOST'],'kn.pr.acornfinance.com') !== false || strpos($_SERVER['HTTP_HOST'],'mykukun.com') !== false)){
				$mndSuffix = 'kukun';
			}
			//print_r($mndSuffix);exit;
			if(isset($mndSuffix) && $mndSuffix != ''){
				//print_r($data);exit(2);
				$response_data = $this->sendTemplateEmail($abanDonedEmailConfig->template_name."-".$mndSuffix,$data,$this->getSendAtTime($abanDonedEmailConfig->send_after));
				//return $response_data;
				//var_dump($response_data);exit('call');
				if(isset($response_data['code']) && $response_data['code'] == 5){
					return $this->sendTemplateEmail($abanDonedEmailConfig->template_name,$data,$this->getSendAtTime($abanDonedEmailConfig->send_after));
				}else{
					return $response_data;
				}
			}else{
				//exit('call2');
				return $this->sendTemplateEmail($abanDonedEmailConfig->template_name,$data,$this->getSendAtTime($abanDonedEmailConfig->send_after));
			}
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Scheduled Abandoned Email','POST','application/json', $data ,$response);
			return $response;
		}
		
	}
	private function getSendAtTime($addTime){
		
		if($addTime == "") {
			$addTime = "+10 minutes";
		}
		
		$currentDateTime = new DateTime();
		 $currentDateTime->modify($addTime);
		//$given->setTimezone(new DateTimeZone("UTC"));
		return $currentDateTime->format("Y-m-d H:i:s e");
	}
	private function getReceiverName($data){
		$fullName = trim(stripslashes(ucwords(strtolower($data['firstname']))));
		$fullName .=' '.trim(stripslashes(ucwords(strtolower($data['lastname']))));
		return $fullName;
	}
	private function capitaliseWords($str){
		return trim(stripslashes(ucwords(strtolower($str))));
	}
	private function sendTemplateEmail($templateName,$data,$sendAt=null,$templateContent=array()){
		// echo "<pre>";print_r($templateName);echo "<br>";
		// echo "<pre>";print_r($data);echo "<br>";
		// echo "<pre>";print_r($sendAt);echo "<br>";
		// echo "<pre>";print_r($templateContent);echo "<br>";
		$adminEmailConfig = (object)$this->mandrillConfigData->error_mail;
		$message = array('template_name'=>$templateName,
							'from_name'=>$this->mandrillConfigData->sender_name,
							'from_email'=>$this->mandrillConfigData->sender_email, //get_option('admin_email'),
							"bcc_address"=>$adminEmailConfig->error_email,
							'to'=> array(
									array('email' => $data['email'], 
										  'name' => $this->getReceiverName($data),
										  'type'=>'to')
										 ),
							'global_merge_vars'=>array(
									array(
										'name'=>'FIRSTNAME',
										'content' => $this->capitaliseWords($data['firstname']),
									),
									array(
										'name'=>'LASTNAME',
										'content' => $this->capitaliseWords($data['lastname']),
									),
									array(
										'name'=>'activationLink',
										'content' => isset($data['activation_link'])?$data['activation_link']:'',
									),
									array(
										'name'=>'forgotLink',
										'content' => isset($data['forgot_link'])?$data['forgot_link']:'',
									),
									array(
										'name'=>'NumberofOffers',
										'content' => isset($data['offerCount'])?$data['offerCount']:'0',
									),
									array(
										'name'=>'AppID',
										'content' => isset($data['AppID'])?$data['AppID']:'',
									),
									array(
										'name'=>'offerStatus',
										'content' => isset($data['offerStatus'])?$data['offerStatus']:'',
									),
									array(
										'name'=>'offerURL',
										'content' => isset($data['offerURL'])?$data['offerURL']:'',
									),
									array(
										'name'=>'accessURL',
										'content' => isset($data['accessURL'])?$data['accessURL']:'',
									),
									array(
										'name'=>'lenderName',
										'content' => isset($data['lenderName'])?$data['lenderName']:'',
									),
									array(
										'name'=>'businessName',
										'content' => isset($data['businessName'])?$data['businessName']:'',
									),
									array(
										'name'=>'AppDate',
										'content' => date('M d Y'),
									),
									array(
										'name'=>'ReturnURL',
										'content' => isset($data['returnUrl'])?$data['returnUrl']:'',
									),
									array(
										'name'=>'expirationdate',
										'content' => date("M j, Y", strtotime(date('M d Y')." +21 days")),
									),
									array(
										'name'=>'UTMContent',
										'content' => isset($data['utm_content'])?$data['utm_content']:'',
									),
									array(
										'name'=>'LOANPURPOS',
										'content' => isset($data['loanpurpose'])?$data['loanpurpose']:'',
									),
									array(
										'name'=>'DEALERID',
										'content' => isset($data['dealerid'])?$data['dealerid']:'',
									)
								),
							);
				if(isset($data['subject'])){
					$message['subject'] = $data['subject'];
				}	
		if(isset($data['global_merge_vars'])){
			foreach($data['global_merge_vars'] as $key=>$value){
				$message['global_merge_vars'][] = array(
										'name'=>$key,
										'content' =>$value,
									);
			}
		}

		// Adding the additional to parameter for admin error email template 24-02-22
		if(isset($templateName) && $templateName == 'admin-error-email'){
			$message['to'][] = array('email' => 'it@acornfinance.com',
									'name' => $adminEmailConfig->first_name,
									'type'=>'to');
			$message['preserve_recipients'] = true;
		}

		$response = $this->objMandrill->messages->sendTemplate($templateName,$templateContent,$message,false,null,$sendAt);	
		$mandrill_curl = new Curl();
		$mandrill_curl->logApiCall('https://mandrillapp.com/api/1.0','POST','application/text',$data,$response);
		return isset($response[0])?$response[0]:$response;	
	}
	public function cancelScheduledEmail($data){
		try{
			//echo "TRY";print_r($data);
			if(isset($data['_id'])){
				$id = $data['_id'];
				return  $this->objMandrill->messages->cancelScheduled($id);
			}else{
				return ['status'=>false,'message'=>'Undefined Index _id','error_object'=>$data];
			}
		}catch(Mandrill_Error $e) {
			//echo "CATCH";print_r($e);
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.' Cancel Scheduled Email','POST','application/json', $data ,$response);
			return $response;
		}
		
		
	}
	public function scheduleForgotEmail($data){
		try{
			$forgotEmailConfig = (object)$this->mandrillConfigData->forgot_email;
			$data['subject'] = $forgotEmailConfig->subject;
			return $this->sendTemplateEmail($forgotEmailConfig->template_name,$data,$this->getSendAtTime($forgotEmailConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleEmailVerification($data){
		try{
			$emailVerificationConfig = (object)$this->mandrillConfigData->email_verification;
			$data['subject'] = $emailVerificationConfig->subject;
			return $this->sendTemplateEmail($emailVerificationConfig->template_name,$data,$this->getSendAtTime($emailVerificationConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleEmailVerificationSME($data){
		try{
			$emailVerificationConfig = (object)$this->mandrillConfigData->email_verification_sme;
			$data['subject'] = $emailVerificationConfig->subject;
			return $this->sendTemplateEmail($emailVerificationConfig->template_name,$data,$this->getSendAtTime($emailVerificationConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleDocumentUploadEmail($data){
		try{
			$gdsPPPConfig = (object)$this->mandrillConfigData->gds_ppp;
			$data['subject'] = $gdsPPPConfig->subject;
			return $this->sendTemplateEmail($gdsPPPConfig->template_name,$data,$this->getSendAtTime($gdsPPPConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleThankYouEmail($data){
		try{
			$thankYouEmailConfig = (object)$this->mandrillConfigData->thank_you_mail;
			if(isset($data['agreeemail']) && $data['agreeemail']){
				  global $configData;
				$objMailChimp = NEW MailChimp();
				// Switching subscriber list based on headway flag
				$objMailChimp->listId = $configData['system']['mail-chimp']['newsletter-list-id']; // PrimeRates Newsletter - Development List ID
				$response = $objMailChimp->saveMemberToList($data);
			}
			$data['subject'] = $thankYouEmailConfig->subject;
			return $this->sendTemplateEmail($thankYouEmailConfig->template_name,$data,$this->getSendAtTime($thankYouEmailConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleNoOffersEmail($data){
		try{
			$noOfferEmailConfig = (object)$this->mandrillConfigData->no_offer_email;
			if(isset($data['agreeemail']) && $data['agreeemail']){
				  global $configData;
				$objMailChimp = NEW MailChimp();
				// Switching subscriber list based on headway flag
				$objMailChimp->listId = $configData['system']['mail-chimp']['newsletter-list-id']; // PrimeRates Newsletter - Development List ID
				$response = $objMailChimp->saveMemberToList($data);
			}
			$data['subject'] = $noOfferEmailConfig->subject;
			return $this->sendTemplateEmail($noOfferEmailConfig->template_name,$data,$this->getSendAtTime($noOfferEmailConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send No Offers Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleThankYouEmailHeadway($data){
		try{
			$mndSuffix = $_SESSION['mndSuffix'];
			$thankYouEmailConfig = (object)$this->mandrillConfigData->thank_you_mail;
			$data['subject'] = $thankYouEmailConfig->subject;
			if(!isset($_SESSION['mndSuffix']) && (strpos($_SERVER['HTTP_HOST'],'kn.pr.acornfinance.com') !== false || strpos($_SERVER['HTTP_HOST'],'mykukun.com') !== false)){
				$mndSuffix = 'kukun';
			}
			if(isset($mndSuffix) && $mndSuffix != ''){
				$data['returnUrl'] = $_SESSION['returnUrl'];
				$response_data = $this->sendTemplateEmail($thankYouEmailConfig->template_name."-".$mndSuffix,$data,$this->getSendAtTime($thankYouEmailConfig->send_after));
				if(isset($response_data['code']) && $response_data['code'] == 5){
					return $this->sendTemplateEmail($thankYouEmailConfig->template_name,$data,$this->getSendAtTime($thankYouEmailConfig->send_after));
				}else{
					return $response_data;
				}
			}else{
				return $this->sendTemplateEmail($thankYouEmailConfig->template_name,$data,$this->getSendAtTime($thankYouEmailConfig->send_after));
			}
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleThankYouEmailPartner($data){
		try{
			$thankYouEmailConfig = (object)$this->mandrillConfigData->thank_you_mail;
			$data['subject'] = $thankYouEmailConfig->subject;
			return $this->sendTemplateEmail($thankYouEmailConfig->template_name,$data,$this->getSendAtTime($thankYouEmailConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleThankYouEmailPhysician($data){
		try{
			$thankYouEmailConfig = (object)$this->mandrillConfigData->physician_thank_you_mail;
			$data['subject'] = "Zalea Loan Inquiry for ".$data['firstname']." ".$data['lastname']." – Application ".$data['AppID']; 
			$data['offerStatus'] = 'Offers Provided';
			$data['email'] = $data['physicianEmail'];
			return $this->sendTemplateEmail($thankYouEmailConfig->template_name,$data,$this->getSendAtTime($thankYouEmailConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleNoOffersEmailHeadway($data){
		try{
			$mndSuffix = $_SESSION['mndSuffix'];
			$noOfferEMailConfig = (object)$this->mandrillConfigData->no_offer_email;
			$data['subject'] = $noOfferEMailConfig->subject;
			if(!isset($_SESSION['mndSuffix']) && (strpos($_SERVER['HTTP_HOST'],'kn.pr.acornfinance.com') !== false || strpos($_SERVER['HTTP_HOST'],'mykukun.com') !== false)){
				$mndSuffix = 'kukun';
			}
			// Added this freeze no offer email template logic override for existing no offer email flow
			if(isset($data['suppressionIndicator']) && $data['suppressionIndicator'] == 'F'){
				$data['subject'] = 'Acorn Finance: Credit Freeze Alert';
				return $this->sendTemplateEmail($noOfferEMailConfig->template_name."-freeze-qa", $data, $this->getSendAtTime($noOfferEMailConfig->send_after));
			}else {
				if (isset($mndSuffix) && $mndSuffix != '') {
					$data['returnUrl'] = $_SESSION['returnUrl'];
					$response_data = $this->sendTemplateEmail($noOfferEMailConfig->template_name . "-" . $mndSuffix, $data, $this->getSendAtTime($noOfferEMailConfig->send_after));
					if (isset($response_data['code']) && $response_data['code'] == 5) {
						return $this->sendTemplateEmail($noOfferEMailConfig->template_name, $data, $this->getSendAtTime($noOfferEMailConfig->send_after));
					} else {
						return $response_data;
					}
				} else {
					return $this->sendTemplateEmail($noOfferEMailConfig->template_name, $data, $this->getSendAtTime($noOfferEMailConfig->send_after));
				}
			}
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleNoOffersEmailPartner($data){
		try{
			$noOfferEMailConfig = (object)$this->mandrillConfigData->no_offer_email;
			$data['subject'] = $noOfferEMailConfig->subject;
			return $this->sendTemplateEmail($noOfferEMailConfig->template_name,$data,$this->getSendAtTime($noOfferEMailConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function scheduleNoOffersEmailPhysician($data){
		try{
			$noOfferEMailConfig = (object)$this->mandrillConfigData->physician_no_offer_email;
			$data['subject'] = "Zalea Loan Inquiry for ".$data['firstname']." ".$data['lastname']." – Application ".$data['AppID']; 
			$data['offerStatus'] = 'No Offers Provided';
			$data['email'] = $data['physicianEmail'];
			return $this->sendTemplateEmail($noOfferEMailConfig->template_name,$data,$this->getSendAtTime($noOfferEMailConfig->send_after));
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.'Send Thank You Email','POST','application/json', $data ,$response);
			return $response;
		}
	}
	public function listScheduledEmails($to){
		try{
			return $this->objMandrill->messages->listScheduled($to);
		}catch(Mandrill_Error $e) {
			$response = ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
			$this->sendErrorEmail($this->urlForErrorEmail.' List Scheduled Email','POST','application/json', $data ,$response);
			return $response;
		}
		
	}
	public function sendErrorEmail($url, $method, $contentType, $arguments ,$response){
		try{
			$errorEmailConfig = (object)$this->mandrillConfigData->error_mail;
			if(json_encode($response)){
				$response = json_encode($response);
			}else{
				$response =$response;
			}
			$data = array(
				'email' =>$errorEmailConfig->error_email,
				'firstname'=>$errorEmailConfig->first_name,
				'lastname'=>$errorEmailConfig->last_name,
				 'global_merge_vars'=>array(
					'URL'=>$url,
					'METHOD'=>$method,
					'CONTENTTYPE'=>$contentType,
					'REQUEST'=>json_encode($arguments),
					'RESPONSE'=>$response
				 )
			);
			if(isset($arguments['subject']) && $arguments['subject'] != ''){
				$data['subject'] = $arguments['subject'];
			}
			return $this->sendTemplateEmail($errorEmailConfig->template_name,$data);
		}catch(Mandrill_Error $e) {
			return ['status'=>false,'message'=>'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),'error_object'=>$e];
		}
	}
	
}