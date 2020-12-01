<?php

use Common\Core\IpAddress;
use AccountService\UserProfile\UserProfileService;

class User_profile_admin extends Admin_Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_service = UserProfileService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }
    
    /**
     * admin login
     */
    public function login(){
        
    }
    
    

}
