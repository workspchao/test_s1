<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

use AccountService\Account\UserType;

class Admin_Base_Controller extends Base_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->_authoriseClient();
        
    }
    
    //override
    protected function _getAdminId($function_code = NULL) {
        return parent::_getUserProfileId(UserType::ADMIN,$function_code);
    }
    
    

}
