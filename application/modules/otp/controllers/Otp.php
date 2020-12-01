<?php

use Common\Core\IpAddress;
use AccountService\Otp\OtpService;

class Otp extends Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_authoriseClient();
        $this->_service = OtpService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

}
