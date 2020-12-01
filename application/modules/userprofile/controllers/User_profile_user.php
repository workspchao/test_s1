<?php

use Common\Core\IpAddress;
use AccountService\UserProfile\UserProfileService;

class User_profile_user extends User_Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_service = UserProfileService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

}
