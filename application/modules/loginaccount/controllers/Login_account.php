<?php

use Common\Core\IpAddress;
use AccountService\LoginAccount\LoginAccountService;

class Login_account extends Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_authoriseClient();
        $this->_service = LoginAccountService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

}
