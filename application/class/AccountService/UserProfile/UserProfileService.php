<?php

namespace AccountService\UserProfile;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use AccountService\Fun\FunAccessType;
use AccountService\AccessToken\TokenSessionType;
use AccountService\LoginAccount\LoginAccountService;

class UserProfileService extends BaseService {

    protected static $_instance = NULL;
    private $_serviceLoginAccount;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('userprofile/User_profile_model');
            self::$_instance = new UserProfileService($_ci->User_profile_model);
        }
        return self::$_instance;
    }
    
    protected function getServiceLoginAccount(){
        if(!$this->_serviceLoginAccount){
            $this->_serviceLoginAccount = LoginAccountService::build();
        }
        $this->_serviceLoginAccount->setIpAddress($this->getIpAddress());
        $this->_serviceLoginAccount->setUpdatedBy($this->getUpdatedBy());
        return $this->_serviceLoginAccount;
    }

    public function addUserProfile(UserProfile $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_ADD_FAIL);
        return false;
    }

    public function deleteUserProfile($id, $isLogic = true) {

        $filter = new UserProfile();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_DELETE_FAIL);
        return false;
    }

    public function updateUserProfile(UserProfile $entity) {

        $filter = new UserProfile();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_FAIL);
        return false;
    }

    public function selectUserProfile(UserProfile $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
        return false;
    }

    public function getUserProfile($id) {

        if($entity = $this->getRepository()->getById($id)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
        return false;
    }
    
    public function updateUserStatus($id, $status){
        
        $filter = new UserProfile();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
            return false;
        }

        $oldEntity->setStatus($status);
        $oldEntity->setUpdatedBy($this->getUpdatedBy());
        $oldEntity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($oldEntity)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_FAIL);
        return false;
    }

    public function updateUserStatusByIds($ids, $status){
        if($this->getRepository()->updateStatusByIds($ids, $status)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_FAIL);
        return false;
    }
    
    public function getUsers(array $user_ids){
        
        if($collection = $this->getRepository()->findByIds($user_ids)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
        return false;
    }


    //根据时间获取注册用户数量
    public function getUserCountByDate($dateFrom, $dateTo){
        
        if($count = $this->getRepository()->getUserCountByDate($dateFrom, $dateTo)){
            return $count;
        }

        return false;
    }

    public function updateUserGroup($id, $userGroupId){
        $entity = new UserProfile();
        $entity->setId($id);
        $entity->setUserGroupId($userGroupId);
        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_FAIL);
        return false;
    }

    public function updateUserGroupByIds($ids, $userGroupId){
        
        if($this->getRepository()->updateUserGroupByIds($ids, $userGroupId)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_FAIL);
        return false;
    }

    /**
     * 查询管理员列表
     * @param \Qsm\AccountService\UserProfile\UserProfile $filterUserProfile
     * @param type $roleCode
     * @param type $orderBy
     * @param type $limit
     * @param type $page
     * @return boolean
     */
    public function selectAdminUserList(UserProfile $filterUserProfile, $orderBy = NULL, $limit = NULL, $page = NULL){
        
        if($collection = $this->getRepository()->selectAdminUserList($filterUserProfile, $orderBy, $limit, $page)){
            
            $arrUsersId = $collection->result->getFieldValues('created_by');
            $collectionUsers = $this->getUsers($arrUsersId);
            if($collectionUsers){
                $collection->result->joinCreatorName($collectionUsers->result);
            }
            $collection->result->rewind();
            
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
        return false;
    }
    

}
