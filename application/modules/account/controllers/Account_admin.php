<?php

use Common\Core\IpAddress;
use Common\Core\BaseDateTime;
use AccountService\Account\AdminAccountService;
use AccountService\Fun\FunCode;
use AccountService\Account\UserType;
use AccountService\UserProfile\UserProfile;
use AccountService\Account\UserStatus;
use Common\Validator\MobileNumberValidator;
use Common\Helper\InputValidator;
use AccountService\Common\MessageCode;

class Account_admin extends Admin_Base_Controller {

    protected $_service;

    function __construct() {
        
        parent::__construct();
        
        $this->_service = AdminAccountService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }
    
    public function login(){
        
        $this->required(array('username','password'));

        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);
        
        if ($result = $this->_service->adminLogin($username, $password)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function logout(){
        
        $admin_id = $this->_getAdminId();

        $this->_service->setUpdatedBy($admin_id);
        
        if ($result = $this->_service->adminLogout($admin_id)) {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    
    /**
     * 
     * @return boolean  
     */
    public function getAccessibleMenuList(){
        
        $admin_id = $this->_getAdminId();
        
        $this->_service->setUpdatedBy($admin_id);

        if( $object = $this->_service->getAccessibleMenuList($admin_id) )
        {
            $this->_respondWithSuccessCode($this->_service->getResponseCode(),array('result' => $object));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

     
    public function getAdminUserList(){
        
        $admin_id = $this->_getAdminId(FunCode::ADMIN_MANAGE);
        
        $this->_service->setUpdatedBy($admin_id);
        
        $username           = $this->input_post("username");
        $accountID          = $this->input_post("accountID");
        $name               = $this->input_post("name");
        $user_status        = $this->input_post("status");
        $date_from          = $this->input_post("date_from");
        $date_to            = $this->input_post("date_to");
        $limit              = $this->_getLimit();
        $page               = $this->_getPage();
        
        $filterUserProfile = new UserProfile();
        $filterUserProfile->setUserType(UserType::ADMIN);
        
        if($accountID){
            $filterUserProfile->setAccountID($accountID);
        }
        if($name){
            $filterUserProfile->setName($name);
        }
        if($user_status){
            if(!in_array($user_status, UserStatus::getUserStatusCodes())){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid Request.'));
                return false;
            }
            $filterUserProfile->setUserStatus($user_status);
        }
        
        $created_from = new BaseDateTime();
        $created_to = new BaseDateTime();
        if(!empty($date_from)){
            $created_from = BaseDateTime::fromString($date_from . " 00:00:00");
        }
        if(!empty($date_to)){
            $created_to = BaseDateTime::fromString($date_to . " 23:59:59");
        }
        $filterUserProfile->setCreatedFrom($created_from);
        $filterUserProfile->setCreatedTo($created_to);
        
        if($result = $this->_service->getAdminUserList($username, $filterUserProfile, NULL, $limit, $page)){
            
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }
        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function addAdminUser(){
        
        $admin_id = $this->_getAdminId(FunCode::ADMIN_MANAGE);
        
        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('username','password','name'));
        
        $username = $this->input_post("username");
        $password = $this->input_post("password");
        $status   = UserStatus::VERIFIED;
        $name     = $this->input_post("name");

        if($username){
            if(strlen($username) <= 6 || strlen($username) >= 50){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid params: username'));
                return false;
            }
        }
        
        // if($status){
        //     if(!in_array($status, UserStatus::getUserStatusCodes())){
        //         $this->_response(InputValidator::constructInvalidParamResponse('Invalid Request.'));
        //         return false;
        //     }
        // }

        list($entityLoginAccount, $entityUserProfile, $password) = $this->_service->createAdminUser($username, $password, $name, $status);
        
        if(!$entityLoginAccount){
            log_message("error", "addAdminUser - createAdminUser fail.");
            $this->_respondWithFailedCode($this->_service->getResponseCode());
            return false;
        }
        
        $result = array(
            'id' => $entityLoginAccount->getId(),
            'login_type' => $entityLoginAccount->getLoginType(),
            'username' => $entityLoginAccount->getUsername(),
            //'password' => $initAdmin->getPassword()->getPassword(),
            'password' => $password,
            'expired_at' => $entityLoginAccount->getPassword()->getExpiredAt()->getString(),
        );

        $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
        return true;
    }
    
    public function getAdminUser(){
        
        log_message("debug", "function Account_admin->getAdminUser begin");
        
        $admin_id = $this->_getAdminId(FunCode::ADMIN_MANAGE);
        
        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('id'));
        
        $id = $this->input_post("id");
        
        if($result = $this->_service->getAdminUser($id)){
            
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }
        
        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function editAdminUser(){
        
        $admin_id = $this->_getAdminId(FunCode::ADMIN_MANAGE);
        
        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('id','username','user_fun','name'));
        
        $id         = $this->input_post("id");
        $username   = $this->input_post("username");
        $password   = $this->input_post("password");
        $status     = $this->input_post("status");
        $userFun    = $this->input_post("user_fun");
        $name       = $this->input_post("name");
        
        
        if($username){
            if(strlen($username) <= 6 || strlen($username) >= 50){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid params: username'));
                return false;
            }
        }
        
        if($status){
            if(!in_array($status, UserStatus::getUserStatusCodes())){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid Request.'));
                return false;
            }
        }


        if($userFun){
            $userFun = json_decode($userFun);

            if(empty($userFun)){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid params: user_fun'));
                return false;
            }
        }

        list($entityLoginAccount, $password) = $this->_service->editAdminUser($id, $username, $password, $name, $userFun, $status);
        
        if(!$entityLoginAccount){
            log_message("error", "addAdminUser - createAdminUser fail.");
            $this->_respondWithFailedCode($this->_service->getResponseCode());
            return false;
        }
        
        $result = array(
            'id'            => $entityLoginAccount->getId(),
            'login_type'    => $entityLoginAccount->getLoginType(),
            'username'      => $entityLoginAccount->getUsername(),
            'password'      => $password,
            'expired_at'    => $entityLoginAccount->getPassword()->getExpiredAt()->getString(),
        );

        $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
        return true;
    }
    
    public function delAdminUser(){
        
        log_message("debug", "function Account_admin->delAdminUser begin");
        
        $admin_id = $this->_getAdminId(FunCode::ADMIN_MANAGE);
        
        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('id'));
        $id = $this->input_post("id");
        
        $result = $this->_service->deleteAdminUser($id);
        
        if(!$result){
            log_message("error", "addAdminUser - delAdminUser fail.");
            $this->_respondWithFailedCode($this->_service->getResponseCode());
            return false;
        }
        
        $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
        return true;
    }
    

    /////////////////////////////////////////////////////转客用户管理////////////////////////////////////////////////////////////////

    //根据手机号创建转客
    public function createAppUser(){
        
        $admin_id = $this->_getAdminId(FunCode::APPUSER_MANAGE);
        
        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('username','password'));
        
        $username = $this->input_post("username");
        $password = $this->input_post("password");
        $nickName = $this->input_post("nick_name");
        
        //check username(mobile)
        $regex = '^1(3|4|5|6|7|8|9)[0-9]\d{8}$^';
        $v = MobileNumberValidator::make($username, $regex);
        if($v->fails()){
            $this->_response(InputValidator::constructInvalidParamResponse('Invalid params: username'));
            return false;
        }

        list($entityLoginAccount, $entityUserProfile, $password) = $this->_service->createAppUser($username, $password, $nickName);
        
        if(!$entityLoginAccount){
            $this->_respondWithFailedCode($this->_service->getResponseCode());
            return false;
        }
        
        $result = array(
            'id' => $entityLoginAccount->getId(),
            'login_type' => $entityLoginAccount->getLoginType(),
            'username' => $entityLoginAccount->getUsername(),
            'password' => $password,
            'expired_at' => $entityLoginAccount->getPassword()->getExpiredAt()->getString(),
        );

        $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
        return true;
    }
    
    public function getAppUserList(){
        
        $admin_id = $this->_getAdminId(FunCode::APPUSER_MANAGE);
        
        $this->_service->setUpdatedBy($admin_id);
        
        $id             = $this->input_post("id");
        $mobile         = $this->input_post("mobile");
        $nickName       = $this->input_post("nick_name");
        $userStatus     = $this->input_post("user_status");
        $dateFrom       = $this->input_post("date_from");
        $dateTo         = $this->input_post("date_to");
        $userGroupId    = $this->input_post("user_group_id");
        $parent         = $this->input_post("parent"); //推荐人: 手机号或者昵称
        $channel        = $this->input_post("channel");
        
        $orderBy        = $this->input_post("order_by");
        $limit          = $this->_getLimit();
        $page           = $this->_getPage();
        
        $filterUserProfile = new UserProfile();
        $filterUserProfile->setUserType(UserType::APPUSER);

        if(!empty($id)){

            $id = json_decode($id);
            $filterUserProfile->setId($id);
        }

        if(!empty($nickName)){
            $filterUserProfile->setNickName($nickName);
        }


        if(!empty($userStatus)){
            if(!in_array($userStatus, UserStatus::getUserStatusCodes())){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid Request.'));
                return false;
            }
            $filterUserProfile->setStatus($userStatus);
        }
        

        if(!empty($userGroupId)){
            $filterUserProfile->setUserGroupId($userGroupId);
        }

        if(!empty($mobile)){
            $filterUserProfile->setMobile($mobile);
        }
        
        if(!empty($channel)){
            $filterUserProfile->setChannel($channel);
        }

        
        $createdFrom  = new BaseDateTime();
        $createdTo    = new BaseDateTime();

        if(!empty($dateFrom)){
            $createdFrom = BaseDateTime::fromString($dateFrom . " 00:00:00");
        }
        if(!empty($dateTo)){
            $createdTo = BaseDateTime::fromString($dateTo . " 23:59:59");
        }
        $filterUserProfile->setCreatedFrom($createdFrom);
        $filterUserProfile->setCreatedTo($createdTo);
        
        if($result = $this->_service->getUserListByAdmin($filterUserProfile, $parent, $orderBy, $limit, $page)){
            
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }
        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function getAppUserRelationship(){
        
        $admin_id = $this->_getAdminId(FunCode::APPUSER_MANAGE);

        // $this->required(array('parent_id'));

        
        $this->_service->setUpdatedBy($admin_id);
        
        $parentId       = $this->input_post("parent_id"); //推荐人id
        $orderBy        = $this->input_post("order_by");
        $level          = $this->input_post('level');     //1徒弟 2徒孙

        if($level !== null && $level !== ''){
            $level = intval($level);
        }
        
        $limit          = $this->_getLimit();
        $page           = $this->_getPage();
    
        if(empty($parentId)){
            $this->_respondWithSuccessCode(MessageCode::CODE_ADMIN_USER_GET_SUCCESS, array('result' => array()));
            return true;
        }

        if($result = $this->_service->getAppUserRelationship($parentId, $level, $orderBy, $limit, $page)){
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }
        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    // public function getAppUser(){
        
    //     log_message("debug", "function Account_admin->getAppUser begin");
        
    //     $admin_id = $this->_getAdminId(FunCode::APPUSER_MANAGE);
        
    //     $this->_service->setUpdatedBy($admin_id);
        
    //     $this->required(array('id'));
        
    //     $id = $this->input_post("id");
        
    //     if($result = $this->_service->getUser($id)){
            
    //         $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
    //         return true;
    //     }
        
    //     $this->_respondWithFailedCode($this->_service->getResponseCode());
    //     return false;
    // }
    
    public function editAppUser(){

        $admin_id = $this->_getAdminId(FunCode::APPUSER_MANAGE);
        
        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('id'));

        $id             = $this->input_post("id");
        $password       = $this->input_post("password");
        $status         = $this->input_post("status");
        $name           = $this->input_post("name");
        $userGroupId    = $this->input_post("user_group_id");
        $remark         = $this->input_post("remark");
        
        
        if($status){
            if(!in_array($status, UserStatus::getUserStatusCodes())){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid Request.'));
                return false;
            }
        }
        
        if($result = $this->_service->editAppUser($id, $password, $name, $status, $userGroupId, $remark)){
            $this->_respondWithSuccessCode($this->_service->getResponseCode());
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function bindInvite(){
       
        $admin_id = $this->_getAdminId(FunCode::APPUSER_MANAGE);
       
        $this->required(array("user_id","invite_code"));
       
        $user_id     = $this->input_post("user_id");
        $invite_code = $this->input_post("invite_code");
       
        if($result = $this->_service->bindInviteCode($user_id, $invite_code)){
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function updateUserGroup(){

        $admin_id = $this->_getAdminId();
        
        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('id'));

        $id             = $this->input_post("id");
        $userGroupId    = $this->input_post("user_group_id");
        $status         = $this->input_post("status");
        

        if(!empty($status)){
            if(!in_array($status, UserStatus::getUserStatusCodes())){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid Request.'));
                return false;
            }
        }


        $id = json_decode($id);
        
        if($result = $this->_service->updateUserGroup($id, $userGroupId, $status)){
            $this->_respondWithSuccessCode($this->_service->getResponseCode());
            return true;
        }

        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
}
