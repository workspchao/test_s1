<?php

use Common\Core\IpAddress;
use AccountService\MessageCommon\MessageCommonService;

class Message_common_admin extends Admin_Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        
        $this->_service = MessageCommonService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

    public function updateMessageCode(){

    	$message = $this->input_post("message");
    	$code = $this->input_post("code");
    	$this->_service->updateMessage($code, $message);
    }

}
