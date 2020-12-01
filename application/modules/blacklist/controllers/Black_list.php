<?php

use Common\Core\IpAddress;
use AccountService\BlackList\BlackListService;

class Black_list extends Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_authoriseClient();
        $this->_service = BlackListService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

}
