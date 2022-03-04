<?php 
global $wpdb;
global $tableName;
require_once 'mandrillHandler.php';
class Curl
{
	private $isResponseAvailable = false;
    private $userSessionId = null;

	public function logApiCall($url,$method,$contentType,$params,$response,$httpcode=200){
		global $wpdb;
		global $tableName;
		global $configData;
		if((isset($configData['system']['log-all-request']) && $configData['system']['log-all-request'] == 1) || $httpcode!=200)
		{
			if(is_array($params) || is_object($params)){
				$requestParams = json_encode($params);
			}else{
				$requestParams = $params;
			}
			$requestParams = $this->maskRequestParams($requestParams);
			if(is_array($response) || is_object($response)){
				$responseParams = json_encode($response);
			}else{
				$responseParams = $response;
			}
			// Hide mailchimp PII logging information
			$mailchimp_request_data = json_decode($requestParams);
			if(isset($mailchimp_request_data->email_address) && !empty($mailchimp_request_data->email_address)){
				$requestParams = json_decode($requestParams);
				@$requestParams->email_address = '*****';
				@$requestParams->merge_fields->FNAME = '*****';
				@$requestParams->merge_fields->LNAME = '*****';
				$requestParams = json_encode($requestParams);
				$responseParams = json_decode($responseParams);
				if(isset($responseParams) && !empty($responseParams)){
				@$responseParams->email_address = '*****';
				@$responseParams->merge_fields->FNAME = '*****';
				@$responseParams->merge_fields->LNAME = '*****';
				}
				$responseParams = json_encode($responseParams);
				
			}
			
			// Hide zalea PII logging information
			$zalea_request_data = json_decode($requestParams);
			if(isset($zalea_request_data->data[0]->zoho_access_token) && !empty($zalea_request_data->data[0]->zoho_access_token)){
				$requestParams = json_decode($requestParams);
				@$requestParams->data[0]->Email = '*****';
				@$requestParams->data[0]->First_Name = '*****';
				@$requestParams->data[0]->Last_Name = '*****';
				@$requestParams->data[0]->Phone = '*****';
				@$requestParams->data[0]->Street = '*****';
				@$requestParams->data[0]->SSN_Last_4 = '*****';
				$requestParams = json_encode($requestParams);
			}
			
			// Hide PPP Application owner PII information
			$ppp_request_data = json_decode($requestParams);
			if(isset($ppp_request_data->Request->owners) && count($ppp_request_data->Request->owners) > 0){
				for($i = 0; $i< count($ppp_request_data->Request->owners); $i++){
					@$ppp_request_data->Request->owners[$i]->ownerFirstName = '*****';
					@$ppp_request_data->Request->owners[$i]->ownerLastName = '*****';
					@$ppp_request_data->Request->owners[$i]->ownerEmail = '*****';
					@$ppp_request_data->Request->owners[$i]->ownerEIN = '*****';
					@$ppp_request_data->Request->owners[$i]->ownerSSN = '*****';
					@$ppp_request_data->Request->owners[$i]->ownerAddress1 = '*****';
				}
				$requestParams = json_encode($ppp_request_data);
			}
			
			// Hide LOS Application coborrower PII information
			/*$los_request_data = json_decode($requestParams);
			if(isset($los_request_data->Request->borrowers) && count($los_request_data->Request->borrowers) > 0){
				for($i = 0; $i< count($los_request_data->Request->borrowers); $i++){
					@$los_request_data->Request->borrowers[$i]->firstName = '*****';
					@$los_request_data->Request->borrowers[$i]->lastName = '*****';
					@$los_request_data->Request->borrowers[$i]->primaryPhoneNumber = '*****';
					@$los_request_data->Request->borrowers[$i]->emailAddress = '*****';
					@$los_request_data->Request->borrowers[$i]->SSN = '*****';
					@$los_request_data->Request->borrowers[$i]->address1 = '*****';
					@$los_request_data->Request->borrowers[$i]->dateOfBirth = '*****';
				}
				$requestParams = json_encode($los_request_data);
			}*/
			
			// Hide PII Data for Headway API call
			$headway_request_data = json_decode($requestParams);
			if(isset($headway_request_data->email) && !empty($headway_request_data->email)){
				$requestParams = json_decode($requestParams);
				@$requestParams->firstName = '*****';
				@$requestParams->firstname = '*****';
				@$requestParams->lastName = '*****';
				@$requestParams->lastname = '*****';
				@$requestParams->email = '*****';
				@$requestParams->ssn = '*****';
				@$requestParams->DOB = '*****';
				@$requestParams->phonenumber = '*****';
				@$requestParams->streetaddress = '*****';
				$requestParams = json_encode($requestParams);
			}
			
			$data = array(
				'request_url'=>$url,
				'method'=>$method,
				'content_type'=>'Content-Type: '.$contentType,
				'request_params'=>$requestParams,
				'response_params'=>$responseParams,
				'session_id' => $this->userSessionId,
				'created_at'=>date('Y-m-d H:i:s'),
				'updated_at'=>date('Y-m-d H:i:s')
			);
			// Check for TransactionType and if its Offers than log it continuously
			$transaction_type_data = json_decode($requestParams,true);
			$transaction_type = isset($transaction_type_data['Request']['TransactionType'])?$transaction_type_data['Request']['TransactionType']:'';
			//$wpdb->insert( $tableName, $data);
			if($transaction_type == 'Offers'){
				$wpdb->insert( $tableName, $data);
			}else{
				/*$selectObject = $wpdb->get_row("select count(*) as records from $tableName where method='$method' AND session_id='$this->userSessionId' and request_url='$url'") ;
				if (isset($selectObject->records) && $selectObject->records >= 1) {
					$where =  array(
						'request_url' => $url,
						'session_id' => $this->userSessionId,
						'method' => $method,
					);
					$wpdb->update( $tableName, $data, $where);
				} else {*/
					$wpdb->insert( $tableName, $data);
				//}
				
			}
			
		}
		
		/*echo $wpdb->last_query ;
		if($wpdb->last_error !== '') {
			$wpdb->print_error();
		}*/
	}
	public function logMessages($message,$sendEmail=false){
		global $wpdb;
		global $tableName;
		$params['Request']['TransactionType'] = 'error_message';
		$params['Request']['Message'] = $message;
		if(is_array($params) || is_object($params)){
			$requestParams = json_encode($params);
		}else{
			$requestParams = $params;
		}
		$data = array(
			'request_url'=>'',
			'method'=>'Post',
			'content_type'=>'Content-Type: text/plain',
			'request_params'=>$requestParams,
			'response_params'=>'',
			'session_id' => session_id(),
			'created_at'=>date('Y-m-d H:i:s'),
			'updated_at'=>date('Y-m-d H:i:s')
		);
		
		$arguments = array();
		if($sendEmail){
			$this->sendErrorMail($url='', $method='Post', 'Content-Type: text/plain', $arguments ,$requestParams);
		}
		$wpdb->insert( $tableName, $data);
					
	}
    public function callApi($url, $method, $contentType='text/plain', $arguments = array(), $encodeData = true, $returnHeaders = false)
    {
		global $configData;
    	//Send Error Mail Exception Handling
		try
		{	
		$uuid = isset($arguments['Request']['uuid']) ? $arguments['Request']['uuid'] : null;
		$userSessionId = session_id();
		$session_id = $uuid.'-'.$userSessionId;
		$this->userSessionId = $session_id;
		$this->logApiCall($url, $method, $contentType, $arguments ,array());

		$curl_request = curl_init();
		if(isset($arguments['data'][0]['zoho_access_token'])){
			$zoho_access_token = $arguments['data'][0]['zoho_access_token'];
			$headers = array("Authorization: Zoho-oauthtoken $zoho_access_token","Content-Type: $contentType");
		}else{
			$headers = array("Content-Type: $contentType");
		}
		if(isset($arguments['destination'])){
			switch ($arguments['destination']){
				case 'mandrill':
					$curl_request = curl_init($url);
					curl_setopt($curl_request, CURLOPT_USERPWD, 'user:' . $arguments['apiKey']);
					unset($arguments['apiKey']);
					break;
				default:
					break;
			}
			unset($arguments['destination']);
		}
        if ($method == 'GET' && $arguments) {
            $url .= "?" . http_build_query($arguments);
        }
        if ($method == 'POST') {
            curl_setopt($curl_request, CURLOPT_POST, 1);
        } else {
            curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, $method );
        }
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_request, CURLOPT_HEADER, false);
		// Set Headway API call Basic auth
		/*if(isset($arguments['api_username']) && isset($arguments['api_password'])){
			curl_setopt($curl_request, CURLOPT_USERPWD, $arguments['api_username'] . ":" . $arguments['api_password']);
		}*/
		if(strpos($url,"api.acornfinance.com") !== false){
			$headwayConfigData = (object)$configData['headway_config'];
			curl_setopt($curl_request, CURLOPT_USERPWD, $headwayConfigData->api_username . ":" . $headwayConfigData->api_password);
		}
        curl_setopt($curl_request, CURLOPT_URL, $url);
		curl_setopt($curl_request, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
        if (!empty($arguments) && $method !== 'GET') {
            if ($encodeData) {
                //encode the arguments as JSON
               $arguments = json_encode($arguments);
			   
            }
            curl_setopt($curl_request, CURLOPT_POSTFIELDS, $arguments);
        }
//echo $arguments;exit;
        $result = curl_exec($curl_request);
		$httpcode = curl_getinfo($curl_request, CURLINFO_HTTP_CODE);
		if ($returnHeaders) {
            //set headers from response
            list($headers, $content) = explode("\r\n\r\n", $result, 2);
            foreach (explode("\r\n", $headers) as $header) {
                header($header);
            }

            //return the nonheader data
            return trim($content);
        }
        curl_close($curl_request);
        //decode the response from JSON
		if($encodeData){
			$response = json_decode($result);
			if(!$response){
				$response = $result;
			}
		}else{
			$response = $result;
		}
        if(is_array($response)){
			$response['http_code'] =  $httpcode;
		}else{
			$response = (array)$response;
			$response['http_code'] =  $httpcode;
			$response =  (object)$response;
		}
		
		if($httpcode!=200 && $httpcode!=201 && $httpcode!=100){
			$this->sendErrorMail($url, $method, $contentType, $arguments ,$response);
		}
				$this->logApiCall($url, $method, $contentType, $arguments ,$response,$httpcode);
			// }
        return $response;
        } //Error Mail
		catch (Exception $e) 
		{
			$this->logMessages("callApi function failed.",true);
			//$content= $e->getMessage();
			$this->sendErrorMail($url, $method, $contentType, $arguments ,$e);
			// echo $e->getMessage(); 
		}
	  //Error Mail
    }
	private function sendErrorMail($url, $method, $contentType, $arguments ,$response){
		$objMandrill = New MandrillHandler();
		$arguments = $this->maskRequestParams($arguments);
		$response = $objMandrill->sendErrorEmail($url, $method, $contentType, $arguments ,$response);
	}
	private function maskRequestParams($params){
		$isObject = false;
		if(is_object($params)){
			$isObject = true;
			$params = (array)$params;
		}else if(is_array($params)){
			$params = $params;
		}else{
			$params = json_decode($params);
			if($params){
				if(is_object($params)){
					$isObject = true;
					$params = (array)$params;
				}
			}
		}
		if(isset($params['Request'])){
			$params['Request'] = (array)$params['Request'];
			$maskFields = array('confirm_ssn','confirmemail','firstname','email','lastname','phonenumber','ssn','streetaddress','birthdate','cb_firstname','cb_email','cb_lastname','cb_phonenumber','cb_ssn','cb_streetaddress','cb_birthdate','cssn','SSN','businessEIN','businessSSN','businessPhone','primaryContactFirstName','primaryContactLastName','primaryContactEmail','firstName','lastName','DOB');
			foreach($params['Request'] as $index=>$value){
				if(in_array($index,$maskFields)){
					$value = trim($value);
					if($value){
						$valueArray = str_split($value);
						$maskedString='';
						foreach($valueArray as $valueChar){
							$maskedString.='*';
						}
						$params['Request'][$index] = $maskedString;
					}
				}
				
			}
		}
		if($isObject){
			$params = (object)$params;
		}
		return json_encode($params);
	}
}
?>
