<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

use Common\Microservice\AccountService\AccountServiceFactory;
use Common\Helper\ResponseHeader;
use Common\Core\IpAddress;
use AccountService\Account\AccountService;
use AccountService\Common\MessageCode;
use AccountService\Account\UserType;

class System_Base_Controller extends Base_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->_authoriseClient();
    }

    //override
    protected function _getUserProfileId($userType = UserType::SYSTEM, $functionCode = NULL)
    {
        $accessToken = $this->clientToken;

        if(empty(trim($accessToken))){
            $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setStatusCode(ResponseHeader::HEADER_UNAUTHORIZED);
            $this->response_message->setMessage('Invalid oauth token credentials.');
            
            log_message("error", "_getUserProfileId fail. accessToken is empty");
            $this->_respondAndTerminate();
            return false;
        }

        $serviceAccount = AccountService::build();
        $serviceAccount->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $serviceAccount->setUpdatedBy(false);
        
        if ($user_profile_id = $serviceAccount->checkAccess($accessToken, $userType, $functionCode)) {
            return $user_profile_id;
        }

        $this->response_message->getHeader()->setStatus(ResponseHeader::HEADER_UNAUTHORIZED);
        $this->response_message->setStatusCode($serviceAccount->getResponseCode());
        $this->response_message->setMessage('Invalid oauth token credentials.');
        
        log_message("error", "_getUserProfileId fail. Invalid token");
        
        $this->_respondAndTerminate();
        return false;
    }
}