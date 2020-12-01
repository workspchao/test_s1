<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

use AccountService\Account\UserType;

class User_Base_Controller extends Base_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->_authoriseClient();
        
    }
    
    protected function _getUserId() {
		return parent::_getUserProfileId(UserType::APPUSER);
    }
}
