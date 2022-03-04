<?php
require_once('curl.php');
class MailChimp 
{
	//public $apiKey = 'e1d53c164dc57cc4d1a6090b32df7f8c-us17';
	public $apiKey = '48744f4cf985c7c629e0ceb4bc8ac41f-us17';
    public $listId = '9cec366794';
	private $contentType = 'application/json';
	private $arguments = [];
	private $objCurl;
	public function __construct() {
		// Add API key for headway domain specific mail-chimp subscription
		if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER['HTTP_HOST'],'pr.acornfinance.com') !== false){	
			$this->apiKey = 'a3707fa5d3fa309ea8ec8744ac179886-us17';
		}
        // Add API key for retain domain specific mail-chimp subscription
		if(strpos($_SERVER["HTTP_HOST"],"apply.retainfinance.com") !== false){	
			$this->apiKey = '81e4a200b4b5101a56b551d28ab3abf8-us10';
		}
        $this->objCurl = new Curl();
	    $this->arguments = ['destination'=>'mandrill','apiKey'=>$this->apiKey];
    }
	private function getMailChimpUrl($emailId){
		$memberId = md5(strtolower(($emailId)));
		$dataCenter = substr($this->apiKey,strpos($this->apiKey,'-')+1);
		return  'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->listId . '/members/'. $memberId;
	}
	public function getMemberDetails($emailID){
		$url= $this->getMailChimpUrl($emailID);
		return $this->objCurl->callApi($url,'GET',$this->contentType,$this->arguments);
	}
	
	public function deleteMemberFromList($emailID){
		$url= $this->getMailChimpUrl($emailID);
		echo $url;
		return $this->objCurl->callApi($url,'DELETE',$this->contentType,$this->arguments);
	}
	public function saveMemberToList($data){
		$url= $this->getMailChimpUrl($data['email']);
		$this->arguments['email_address'] = $data['email'];
		$this->arguments['status'] =  isset($data['status'])?$data['status']:'subscribed';
		if(isset($data['firstname'])){
			$this->arguments['merge_fields']['FNAME'] = $data['firstname'];
		} 
		if(isset($data['lastname'])){
			$this->arguments['merge_fields']['LNAME'] = $data['lastname'];
		}
		if(isset($data['phonenumber'])){
			$this->arguments['merge_fields']['PHONE'] = $data['phonenumber'];
		}		
		if(isset($data['application_id'])){
			$this->arguments['merge_fields']['APPID'] = $data['application_id'];
		} 
		if(isset($data['application_date'])){
			$this->arguments['merge_fields']['APPDATE'] = $data['application_date'];
			$this->arguments['merge_fields']['EXPIREDATE'] = date("m/d/Y", strtotime($data['application_date']." +21 days"));
		} 
		if(isset($data['offers'])){
			$this->arguments['merge_fields']['OFFERS'] = $data['offers'];
		} 
		if(isset($data['offer_selected_date'])){
			$this->arguments['merge_fields']['OFFERDATE'] = $data['offer_selected_date'];
		} 
		if(isset($data['affiliateid'])){
			$this->arguments['merge_fields']['AFFID'] = $data['affiliateid'];
		}
		if(isset($data['testGroup'])){
			$this->arguments['merge_fields']['TESTGROUP'] = $data['testGroup'];
		}
		if(isset($data['partnerid'])){
			$this->arguments['merge_fields']['PARTNERID'] = $data['partnerid'];
		} 
		if(isset($data['loanpurpose'])){
			$this->arguments['merge_fields']['LOANPURPOS'] = $data['loanpurpose'];
		} 
		if(isset($data['subPurpose'])){
			$this->arguments['merge_fields']['SUBPURPOSE'] = $data['subPurpose'];
		} 
		if(isset($data['loanamount'])){
			$this->arguments['merge_fields']['LOANAMOUNT'] = $data['loanamount'];
		} 
		if(isset($data['number_of_lenders'])){
			$this->arguments['merge_fields']['NUMBLENDER'] = $data['number_of_lenders'];
		} 
		if(isset($data['number_of_offers'])){
			$this->arguments['merge_fields']['NUMBOFFERS'] = $data['number_of_offers'];
		}
		if(isset($data['applicant_credit_score'])){
			$this->arguments['merge_fields']['CR_SCORE'] = $data['applicant_credit_score'];
		}
		if(isset($data['cb_indicator'])){
			$this->arguments['merge_fields']['CB_INDICAT'] = $data['cb_indicator'];
		}
		if(isset($data['dealerid'])){
			$this->arguments['merge_fields']['DEALERID'] = $data['dealerid'];
		}
		return $this->objCurl->callApi($url,'PUT',$this->contentType,$this->arguments); 
	}
	public function updateMemberToList($data){
		$url= $this->getMailChimpUrl($data['email']);
		$this->arguments['email_address'] = $data['email'];
		$this->arguments['status'] =  isset($data['status'])?$data['status']:'subscribed';
		if(isset($data['offer_selected_date'])){
			$this->arguments['merge_fields']['OFFERDATE'] = $data['offer_selected_date'];
		}
		if(isset($data['lender_name'])){
			$this->arguments['merge_fields']['LENDER'] = $data['lender_name'];
		}
		if(isset($data['loanamount'])){
			$this->arguments['merge_fields']['LOANSELECT'] = $data['loanamount'];
		}
		return $this->objCurl->callApi($url,'PUT',$this->contentType,$this->arguments);
	}
	public function processCompleteForm($data){
		$postData = $data;
		$postData['status'] = 'unsubscribed';
		$response = $this->saveMemberToList($postData);
		$response = $this->deleteMemberFromList($data['email']);
		if(isset($data['agreeemail']) && ($data['agreeemail']===true)){
			$this->listId = '228ceb2d42'; // PrimeRates Newsletter - Development List ID
			$this->saveMemberToList($data);
		}
		$this->listId = '5da6879de5'; // Complete App - Development List ID
		return $this->saveMemberToList($data);
	}
} 
?>