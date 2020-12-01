<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

use Common\Helper\InputValidator;
use Common\Helper\ResponseHeader;
use Common\Helper\ResponseMessage;
use AccountService\MessageCommon\MessageCommonService;
//use Common\Microservice\AccountService\AccountService;
use AccountService\UserProfile\UserProfileService;
use AccountService\VersionControl\VersionControlService;
use AccountService\AccessToken\TokenSessionType;
use AccountService\Fun\FunCode;
use AccountService\Fun\FunAccessType;
use AccountService\Account\UserType;

class Guest_Base_Controller extends Base_Controller {
    
    public function __construct() {
        parent::__construct();
        
        $this->_authoriseClient();
        
    }
}
