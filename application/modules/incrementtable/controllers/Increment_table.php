<?php

use Common\Core\IpAddress;
use AccountService\IncrementTable\IncrementTableService;

class Increment_table extends Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_authoriseClient();
        $this->_service = IncrementTableService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

}
