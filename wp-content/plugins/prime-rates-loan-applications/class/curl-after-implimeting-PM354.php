<?php
//global $wpdb;
//global $tableName;
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

            $selectObject = $wpdb->get_row("select count(*) as records from $tableName where method='$method' AND session_id='$this->userSessionId' and request_url='$url'") ;
            if (isset($selectObject->records) && $selectObject->records >= 1) {
                $where =  array(
                    'request_url' => $url,
                    'session_id' => $this->userSessionId,
                    'method' => $method,
                );
                $wpdb->update( $tableName, $data, $where);
            } else {
            $wpdb->insert( $tableName, $data);
        }


        }

        /*echo $wpdb->last_query ;
        if($wpdb->last_error !== '') {
            $wpdb->print_error();
        }*/
    }
    public function callApi($url, $method, $contentType='text/plain', $arguments = array(), $encodeData = true, $returnHeaders = false)
    {

        //Send Error Mail Exception Handling
        try {
            $ssn = isset($arguments['Request']['ssn']) ? $arguments['Request']['ssn'] : null;
            $userSessionId = session_id();
            $session_id = $ssn.'-'.$userSessionId;
            $this->userSessionId = $session_id;
            $this->logApiCall($url, $method, $contentType, $arguments ,array());

            $curl_request = curl_init();
            $headers = array("Content-Type: $contentType");
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
            curl_setopt($curl_request, CURLOPT_URL, $url);
            curl_setopt($curl_request, CURLOPT_TIMEOUT, 30);
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
            if($httpcode!=200){
                $this->sendErrorMail($url, $method, $contentType, $arguments ,$response);
            } else {
            $this->logApiCall($url, $method, $contentType, $arguments ,$response,$httpcode);
            }
            return $response;
        } //Error Mail
        catch (Exception $e)
        {
            //$content= $e->getMessage();
            $this->sendErrorMail($url, $method, $contentType, $arguments ,$e);
            // echo $e->getMessage();
        }
        //Error Mail
    }
    private function sendErrorMail($url, $method, $contentType, $arguments ,$response){
        $objMandrill = New MandrillHandler();
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
            $maskFields = array('confirm_ssn','confirmemail','firstname','email','lastname','phonenumber','ssn','streetaddress');
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
