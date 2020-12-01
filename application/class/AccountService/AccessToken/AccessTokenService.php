<?php

namespace AccountService\AccessToken;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use AccountService\LoginAccount\LoginAccount;
use AccountService\AccessToken\TokenExpirationGetter;

class AccessTokenService extends BaseService {

    protected static $_instance = NULL;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('accesstoken/Access_token_model');
            self::$_instance = new AccessTokenService($_ci->Access_token_model);
        }
        return self::$_instance;
    }

    public function addAccessToken(AccessToken $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_ADD_FAIL);
        return false;
    }

    public function deleteAccessToken($id, $isLogic = true) {

        $filter = new AccessToken();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_DELETE_FAIL);
        return false;
    }

    public function updateAccessToken(AccessToken $entity) {

        $filter = new AccessToken();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_UPDATE_FAIL);
        return false;
    }

    public function selectAccessToken(AccessToken $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_NOT_FOUND);
        return false;
    }

    public function getAccessToken($id) {

        if($entity = $this->getRepository()->getById($id)){
            $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_NOT_FOUND);
        return false;
    }

    public function getAccessTokenByToken($token){
        
        if($entity = $this->getRepository()->findByToken($token)){
            $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_ACCESS_TOKEN_NOT_FOUND);
        return false;
    }
    
    public function getByLoginAccountID($login_account_id){
        $token = $this->getRepository()->findByLoginAccountID($login_account_id);
        if ($token) {
            return $token;
        }

        return false;
    }

    public function getByLoginAccountIDs($login_account_id){
        $token = $this->getRepository()->findByLoginAccountIDs($login_account_id);
        if ($token) {
            return $token;
        }

        return false;
    }
    
    public function generate(LoginAccount $account, $userType) {
        
        $new = false;
        $valid_period_m = TokenExpirationGetter::get($userType);
        
        if (!$token = $this->_getExistingToken($account->getId())) {
            //create new token if does not exists
            $new = true;
            $token = AccessToken::createFromLoginAccount($account, $valid_period_m);
        }
        else {
            $oriToken = clone($token);
        }

        //loop to ensure no access
        if (!$this->_generateUniqueToken($token)) {
            return false;
        }

        //update expiration date
        $this->_updateExpirationDate($token, $valid_period_m);
        
        if ($new) {
            //insert new access token
            $token->setCreatedBy($this->getUpdatedBy());
            if (!$this->getRepository()->insert($token)){
                log_message("error", "insert access token fail => " . json_encode($token));
                return false;
            }
        }
        else {
            //update access token
            $token->setUpdatedBy($this->getUpdatedBy());
            if (!$this->getRepository()->update($token)) {
                log_message("error", "update access token fail => " . json_encode($token));
                return false;
            }
        }

        return $token;
    }
    
    public function checkAccessToken($token, $userType) {
        
        if ($entityToken = $this->getRepository()->findByToken($token)) {
            if ($entityToken->isValid()) {
                $this->_extendTokenExpiration($entityToken,$userType);
                return $entityToken;
            }
        }

        return false;
    }
    
    public function getExistingToken(LoginAccount $entityLoginAccount){
        return $this->_getExistingToken($entityLoginAccount->getId());
    }

    
    /**
     * invalidate all token
     * @param LoginAccount $loginAccount
     * @return type
     */
    public function invalidateAll(LoginAccount $loginAccount) {
        //find all token by login account
        $filter = new AccessToken();
        $filter->setLoginAccountId($loginAccount->getId());
        
        if($collection = $this->selectAccessToken($filter)){
            foreach ($collection->result as $entityAccessToken) {
                
                //if not expired
                if($entityAccessToken->isExpired() == false){
                    //set expired_at to now
                    $entityAccessToken->setExpired();
                    
                    if(!$this->updateAccessToken($entityAccessToken)){
                        log_message("error", "invalidateAll - update access token failed " . json_encode($entityAccessToken));
                        return false;
                    }
                }
            }
        }
        return true;
    }

    
    protected function _tokenExists($token) {
        return ($this->getRepository()->findByToken($token) !== false);
    }
    
    protected function _getExistingToken($login_account_id) {
        
        $token = $this->getRepository()->findByLoginAccountID($login_account_id);
        if ($token) {
            return $token;
        }

        return false;
    }

    /**
     * 
     * @param \AccountService\AccessToken\AccessToken $entityToken
     * @return type
     */
    protected function _extendTokenExpiration(AccessToken $entityToken, $userType) {
        
        $valid_period_m = TokenExpirationGetter::get($userType);
        if (is_numeric($valid_period_m)) {
            $this->_updateExpirationDate($entityToken, $valid_period_m);
            $this->updateAccessToken($entityToken);
        }
    }
    
    /*
     * Make sure the generated token is unique
     */
    protected function _generateUniqueToken(AccessToken $entityToken) {
        if (!$this->_tokenExists($entityToken->getToken()))
            return $entityToken;

        //loop to ensure no access
        $max_attempt = 100;
        $attempt = 0;
        do {
            $entityToken->generate();
            $attempt++;
        } while ($flag = $this->_tokenExists($entityToken->getToken())
        and $attempt < $max_attempt);

        if ($flag == false)
            return $entityToken;

        return false;
    }
    
    protected function _updateExpirationDate(AccessToken $entityToken, $expiredIn) {//no expiry
        $dt = new BaseDateTime();
        if ($expiredIn != NULL) {
            $dt = BaseDateTime::now();
            $dt->addMinute($expiredIn);
        }

        $entityToken->setExpiredAt($dt);
    }

}
