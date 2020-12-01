<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

use Common\Helper\InputValidator;
use Common\Helper\ResponseHeader;
use Common\Helper\ResponseMessage;
use Common\Core\IpAddress;
use AccountService\MessageCommon\MessageCommonService;
use AccountService\Account\AccountService;
use AccountService\VersionControl\VersionControlService;
use Common\Helper\GuidGenerator;
use AccountService\Common\MessageCode;

class Base_Controller extends CI_Controller {
    
    protected $lang;
    protected $clientToken;
    protected $response_message;
    
    protected $clientInfo = array();
    protected $defaultRes = array("result" => null, "total" => 0);
    
    public function __construct() {
        
        parent::__construct();
        
        //where to make sure initiated as code 500
        http_response_code(500);
        date_default_timezone_set('Asia/Shanghai');
        
        //log_message("debug", "REQUEST SERVER INFO:" . json_encode($_SERVER));
        
        $this->clientInfo["app_id"]     = $this->input->get_request_header(ResponseHeader::FIELD_X_APP, true);
        $this->clientInfo["version"]    = $this->input->get_request_header(ResponseHeader::FIELD_X_VERSION, true);
        $this->clientInfo["platform"]   = $this->input->get_request_header(ResponseHeader::FIELD_X_PLATFORM, true);
        $this->clientInfo["token"]      = $this->input->get_request_header(ResponseHeader::FIELD_X_AUTHORIZATION, true);
        
        $user_agent = $this->input->user_agent();
        $user_referrer = $this->agent->referrer();
        
        $ip_address = $this->input->ip_address();
        $entityIpAddress = IpAddress::fromString($ip_address);
        $ip_arr = array("ip_str" => $entityIpAddress->getString(), "ip_int" => $entityIpAddress->getInteger());
        
        $tmpLogEntity = array(
            "request_url" => uri_string(),
            "request_app:" => json_encode($this->clientInfo),
            "request_ip" => $ip_arr,
            "request_ua" => $user_agent,
            "request_refer" => $user_referrer,
            "request_get" => $_GET,
            "request_post" => $_POST
        );
        
        log_message("debug", "|NEW_REQUEST_INCOMING|" . json_encode($tmpLogEntity) . PHP_EOL.
                "|==================================================");
        
        $this->response_message = new ResponseMessage();
        
    }

    protected function input_post($index = NULL, $xss_clean = TRUE) {
        return $this->input->post($index, $xss_clean);
    }

    protected function input_get($index = NULL, $xss_clean = TRUE) {
        return $this->input->get($index, $xss_clean);
    }

    protected function required($rules = NULL, $param = NULL, $checkZero = TRUE) {

        if (empty($param))
            $param = $this->input_post();
        if (empty($param))
            $param = $this->input_get();
        
        $validator = InputValidator::make($param, $rules, $checkZero);

        if ($validator->fails()) {
            $this->_response($validator->getErrorResponse());
            $this->_respondAndTerminate();
        }

        return true;
    }

    protected function requiredGet($rules = NULL, $param = NULL, $checkZero = TRUE) {

        if (empty($param))
            $param = $this->input_get();

        $validator = InputValidator::make($param, $rules, $checkZero);

        if ($validator->fails()) {
            $this->_response($validator->getErrorResponse());
            $this->_respondAndTerminate();
        }

        return true;
    }

    protected function _getIpAddress() {
        return $this->input->ip_address();
    }

    protected function _getUserAgent() {
        return $this->input->user_agent();
    }

    protected function _getLang() {
        $this->lang = NULL;
        if ($language = $this->input->get_request_header(ResponseHeader::FIELD_X_LANGUAGE, true))
            $this->lang = $language;

        return $this->lang;
    }

    protected function _getMessageByCode($code, $language = NULL) {
        if ($language == NULL)
            $language = $this->_getLang();

        $serviceMessageCommon = MessageCommonService::build();

        if($message = $serviceMessageCommon->getMessage($code, $language))
        {
            return $message;
        }
        return null;
    }
    
    protected function set_output() {
        $this->response_message->getHeader()->setField(ResponseHeader::FIELD_CONTENT_TYPE, ResponseHeader::VALUE_JSON);
        $this->response_message->getHeader()->setField(ResponseHeader::FIELD_CACHE_CONTROL, 'no-store');

        $this->output->set_status_header($this->response_message->getHeader()->getStatus());
        foreach ($this->response_message->getHeader() AS $fieldvalue) {
            $this->output->set_header($fieldvalue);
        }
        
        $this->output->set_output($this->response_message->getJsonMessage());
    }
    
    /**
     * call this function will immediately display output and terminate
     */
    protected function _respondAndTerminate() {
        $this->set_output();
        $this->output->_display();
        //todo better way to terminate instead of die?
        die();
    }
    
    /**
     * 
     * @param ResponseMessage $response
     */
    protected function _response(ResponseMessage $response) {
        $this->response_message = $response;
        $this->set_output();
    }

    protected function _respond($status_code = ResponseHeader::HEADER_SUCCESS) {
        $this->response_message->getHeader()->setStatus($status_code);
        $this->set_output();
    }

    protected function _respondWithCode($status, $code, $status_code = ResponseHeader::HEADER_SUCCESS, $result = NULL, $language = NULL, $preMessage = NULL) {
        $this->response_message->setStatus($status);
        $message = null;
        if ($code != NULL) {
            $this->response_message->setStatusCode($code);
            if ($preMessage)
                $message = $preMessage;
            else
                $message = $this->_getMessageByCode($code, $language);
        }
        else{
            if ($preMessage)
                $message = $preMessage;
        }
        $this->response_message->setMessage($message, $result);

        $this->_respond($status_code);
    }
    
    protected function _respondWithSuccessCode($code, $result = NULL, $lang = NULL, $preMessage = NULL) {
        $this->_respondWithCode(true, $code, ResponseHeader::HEADER_SUCCESS, $result, $lang, $preMessage);
    }

    protected function _respondWithFailedCode($code, $result = NULL, $lang = NULL, $preMessage = NULL) {
        $this->_respondWithCode(false, $code, ResponseHeader::HEADER_SUCCESS, $result, $lang, $preMessage);
    }

    
    /**
     * authorise client (app_id)
     * @return boolean
     */
    protected function _authoriseClient() {
        
        $app = $this->clientInfo["app_id"];
        $version = $this->clientInfo["version"];
        $platform = $this->clientInfo["platform"];

        if (empty(trim($app))) {
            $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setStatusCode(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setMessage('Invalid app client.');

            log_message("error", "_authoriseClient -> Invalid app client.");
            
            $this->_respondAndTerminate();
            return false;
        }

        $serviceVersionControl = VersionControlService::build();
        list($state, $result) = $serviceVersionControl->authorizeClient($app, $version, $platform);
        if ($state === true) {
            if (isset($result['token']))
                $this->clientToken = $result['token'];

            return $result;
        }
        else {
            
            log_message("error", "_authoriseClient -> authorizeClient fail.");

            $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setStatusCode(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setMessage('Invalid app client.');

            //$this->_respondWithCode(false, $serviceVersionControl->getResponseCode(), ResponseHeader::HEADER_UNAUTHORIZED);
            $this->_respondAndTerminate();
            return false;
        }
    }

    protected function _getUserProfileId($userType = NULL, $functionCode = NULL)
    {
        $accessToken = $this->clientInfo["token"];
        if(empty(trim($accessToken))){
            $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setStatusCode(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setMessage('Invalid oauth token credentials.');
            
            log_message("error", "_getUserProfileId fail. accessToken is empty");
            $this->_respondAndTerminate();
            return false;
        }

        $serviceAccount = AccountService::build();
        $serviceAccount->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $serviceAccount->setUpdatedBy(false);
        
        
        if ($user_profile_id = $serviceAccount->checkAccess($accessToken, $userType, $functionCode)) {
            return $user_profile_id;
        }
        else{
            $this->_respondWithFailedCode($serviceAccount->getResponseCode());
            $this->_respondAndTerminate();
            return false;
        }
        
        $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_UNAUTHORIZED);
        $this->response_message->setStatusCode($serviceAccount->getResponseCode());
        $this->response_message->setMessage('Invalid oauth token credentials.');
        
        log_message("error", "_getUserProfileId fail. Invalid token");
        
        $this->_respondAndTerminate();
        return false;
    }
    
    protected function _getLimit() {
        $limit = 0;
        if ($this->input_post('limit'))
            $limit = $this->input_post('limit');
        elseif ($this->input_get('limit'))
            $limit = $this->input_get('limit');

        if (is_numeric($limit)) {
            if ($limit == MAX_VALUE)
                return MAX_VALUE;

            if ($limit > 0)
                return $limit;
        }

        return DEFAULT_LIMIT;
    }

    protected function _getPage() {
        $page = 0;
        if ($this->input_post('page'))
            $page = $this->input_post('page');
        elseif ($this->input_get('page'))
            $page = $this->input_get('page');

        if (is_numeric($page)) {
            if ($page > 0)
                return $page;
        }

        return DEFAULT_PAGE;
    }

    protected function uploadToServerLocal($file = '', $folderName = NULL)
    {
        if (isset($_FILES[$file])) {
            $name      = time();

            $path = './upload/';
            if($folderName !== NULL)
                $path = $path . "$folderName/";

            if(!is_dir($path)) //create the folder if it's not already exists
            {
                mkdir($path,0755,TRUE);
            }

            $config['file_name']     = mt_rand().$name;
            $config['upload_path']   = $path;
            $config['overwrite']     = TRUE;
            $config['allowed_types'] = 'gif|jpg|png|csv|xls|xlsx';
            $config['max_size']      = '2000';
            $config['max_width']     = '2000';
            $config['max_height']    = '2000';

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload($file)) {
                $error = array('error' => $this->upload->display_errors('', ''));
                return $error;
            } else {
                return $this->upload->data();
            }
        }
        return false;
    }

    protected function _urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    protected function _urlsafe_b64decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    protected function startWith($str, $needle) {
        return strpos($str, $needle) === 0;
    }

    protected function isUUID($str) {
        return preg_match("/[0-9a-fA-F]{8}(-[0-9a-fA-F]{4}){3}-[0-9a-fA-F]{12}/", $str);
    }
    
    protected function _returnObsoleteFunction()
    {
        $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_NOT_FOUND);
        $this->response_message->setStatusCode(ResponseHeader::HEADER_NOT_FOUND);
        $this->response_message->setMessage('Obsolete function');
        $this->set_output();
        return false;
    }
    
    protected function log_benchmark($start, $mark, $fun, $action){
        if(in_array(ENVIRONMENT, array('local', 'development', 'testing'))){
            $this->benchmark->mark($mark);
            $elapsed_time = $this->benchmark->elapsed_time($start, $mark);
            $elapsed_time = number_format($elapsed_time, 6);
            log_message("debug", "$fun - use: $elapsed_time - $action");
        }
    }
}
