<?php

use Common\Core\IpAddress;
use AccountService\AccessToken\AccessTokenService;

class Access_token extends Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_authoriseClient();
        $this->_service = AccessTokenService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

}
