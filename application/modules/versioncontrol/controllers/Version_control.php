<?php

use Common\Core\IpAddress;
use AccountService\VersionControl\VersionControlService;

class Version_control extends Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_authoriseClient();
        $this->_service = VersionControlService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

}
