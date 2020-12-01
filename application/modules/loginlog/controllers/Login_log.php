<?php

use Common\Core\IpAddress;
use AccountService\LoginLog\LoginLogService;

class Login_log extends Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_authoriseClient();
        $this->_service = LoginLogService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

}
