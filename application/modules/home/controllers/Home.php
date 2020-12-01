<?php

use Common\Core\IpAddress;
use Common\Helper\InputValidator;
use AccountService\Fun\Fun;
use AccountService\Fun\FunService;
use AccountService\Fun\FunCode;
use AccountService\Fun\FunType;

class Home extends Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_service = \AccountService\UserProfile\UserProfileService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

    public function index(){
        
        $data = array();
        $data['username'] = '';
        
        $filter = new \AccountService\UserProfile\UserProfile();
        $filter->setId(2);
        
        if($collection = $this->_service->selectUserProfile($filter, null, 1, 1)){
            $user = $collection->result->current();
            $user instanceof \AccountService\UserProfile\UserProfile;
            $username = $user->getName();
            $data['username'] = $username;
        }
        
        $this->load->view('index', $data);
        return true;
    }
    
}
