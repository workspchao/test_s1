<?php

namespace AccountService\LoginAccount;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use AccountService\AccessToken\AccessTokenService;

class LoginAccountService extends BaseService {

    protected static $_instance = NULL;
    private $_serviceAccessToken;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('loginaccount/Login_account_model');
            self::$_instance = new LoginAccountService($_ci->Login_account_model);
        }
        return self::$_instance;
    }
    
    protected function getServiceAccessToken(){
        if(!$this->_serviceAccessToken){
            $this->_serviceAccessToken = AccessTokenService::build();
        }
        $this->_serviceAccessToken->setIpAddress($this->getIpAddress());
        $this->_serviceAccessToken->setUpdatedBy($this->getUpdatedBy());
        return $this->_serviceAccessToken;
    }

    public function addLoginAccount(LoginAccount $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_ADD_FAIL);
        return false;
    }

    public function deleteLoginAccount($id, $isLogic = true) {

        $filter = new LoginAccount();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_DELETE_FAIL);
        return false;
    }

    public function updateLoginAccount(LoginAccount $entity) {

        $filter = new LoginAccount();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_UPDATE_FAIL);
        return false;
    }

    public function selectLoginAccount(LoginAccount $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
        return false;
    }

    public function selectLoginAccountByUserId($user_id, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if(!$user_id){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
            return false;
        }
        
        $filter = new LoginAccount();
        $filter->setUserId($user_id);
        
        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
        return false;
    }

    public function getLoginAccount($id) {

        if($entity = $this->getRepository()->getById($id)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
        return false;
    }

    public function getByAccessToken($token, $userType) {
        $serviceAccessToken = $this->getServiceAccessToken();
        if ($entityToken = $serviceAccessToken->checkAccessToken($token, $userType)) {
            if ($entityLogin = $this->getLoginAccount($entityToken->getLoginAccountId())) {
                return array($entityLogin, $entityToken);
            }
        }

        return false;
    }

    public function getLoginAccountByUserId($user_id, $login_type, $app_id = null){
        if(empty($user_id)){
            return false;
        }
        $filter = new LoginAccount();
        $filter->setUserId($user_id);
        $filter->setLoginType($login_type);
        $filter->setAppId($app_id);
        
        if($collection = $this->selectLoginAccount($filter, null, 1, 1)){
            return $collection->result->current();
        }
        return false;
    }
    
    public function getLoginAccountByUserName($username, $login_type = LoginAccountLoginType::USERNAME, $app_id = null){
        if(empty($username)){
            return false;
        }
        $filter = new LoginAccount();
        $filter->setUsername($username);
        $filter->setLoginType($login_type);
        $filter->setAppId($app_id);
        
        if($collection = $this->selectLoginAccount($filter, null, 1, 1)){
            return $collection->result->current();
        }
        return false;
    }
    
}
