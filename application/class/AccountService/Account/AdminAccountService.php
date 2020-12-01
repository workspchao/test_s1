<?php

namespace AccountService\Account;

use AccountService\Common\MessageCode;
use AccountService\LoginAccount\LoginAccountLoginType;
use AccountService\LoginLog\LoginLogStatus;
use AccountService\LoginLog\LoginLogType;
use AccountService\LoginLog\LoginLog;
use AccountService\UserProfile\UserProfile;
use AccountService\LoginAccount\LoginAccount;
use AccountService\Fun\FunType;
use AccountService\IncrementTable\IncrementIDAttribute;
use Common\ValueObject\PasswordObj;
use AccountService\Fun\Fun;
use AccountService\Fun\FunService;
use AccountService\UserFun\UserFun;
use Common\Core\IpAddress;
use AccountService\UserInvite\UserInvite;
use AccountService\UserDailyStatics\UserDailyStaticsService;

class AdminAccountService extends AccountService {

    protected static $_instance = NULL;

    function __construct() {
        
    }

    public static function build() {
        if (self::$_instance == NULL) {
            self::$_instance = new AdminAccountService();
        }
        return self::$_instance;
    }

    public function adminLogin($username, $password, $login_type = LoginAccountLoginType::USERNAME) {

        //check black list
        if (!$this->_checkBlackList($this->getIpAddress())) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "user has been blacklisted (ipaddress). $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        //check login account if exists
        $serviceLoginAccount = $this->_getServiceLoginAccount();
        if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {

            //login log?
            $this->_addLoginLog(LoginLogStatus::FAILED, LoginLogType::LOGIN);
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "user login account not found. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $user_profile_id = $entityLoginAccount->getUserId();

        $this->setUpdatedBy($user_profile_id);

        //this screen can only be done after user as been identified by userID
        if (!$this->_checkBlackList(NULL, $user_profile_id)) {
            
            $tmpLogEntity = json_encode(array("username" => $username, "login_type" => $login_type));
            log_message("error", "user has been blacklisted (user_id). $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $filter = new UserProfile();
        $filter->setId($user_profile_id);

        $serviceUserProfile = $this->_getServiceUserProfile();
        if (!$collection = $serviceUserProfile->selectUserProfile($filter)) {
            
            $tmpLogEntity = json_encode(array("id" => $user_profile_id, "username" => $username, "login_type" => $login_type));
            log_message("error", "user profile not found. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $entityUserProfile = $collection->result->current();

        //
        if($entityUserProfile->getUserType() != UserType::ADMIN){
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        if (!$entityLoginAccount->authenticate($password)) {
            
            $tmpLogEntity = json_encode(array("id" => $user_profile_id, "username" => $username, "login_type" => $login_type));
            log_message("error", "user password is incorrect. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGIN, $entityLoginAccount);

        if ($entityAccessToken = $this->_proceedToLogin($entityLoginAccount, UserType::ADMIN)) {

            $userInfo = $entityUserProfile->getSelectedField(array('name', 'user_type', 'accountID', 'user_status'));
            $tokenInfo = $entityAccessToken->getSelectedField(array('session_type', 'access_type', 'token', 'expired_at'));

            $this->setResponseCode(MessageCode::CODE_LOGIN_SUCCESS);
            return array('user' => $userInfo, 'access' => $tokenInfo);
        }
        $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
        return false;
    }

    public function adminLogout($user_profile_id, $login_type = null) {

        //check login account if exists

        $filterLoginAccount = new LoginAccount();
        $filterLoginAccount->setUserId($user_profile_id);
        if (!empty($login_type)) {
            $filterLoginAccount->setLoginType($login_type);
        }

        $serviceLoginAccount = $this->_getServiceLoginAccount();
        if (!$collectionLoginAccount = $serviceLoginAccount->selectLoginAccount($filterLoginAccount)) {
            
            $tmpLogEntity = json_encode(array("user_id" =>  $user_profile_id, "login_type" => $login_type));
            log_message("error", "adminLogout fail, login account not found. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_LOGIN_FAIL);
            return false;
        }

        if (!empty($login_type)) {
            //logout one account
            $entityLoginAccount = $collectionLoginAccount->result->current();
            if (!$this->_logoutAccount($entityLoginAccount)) {
                
                $tmpLogEntity = json_encode($entityLoginAccount);
                log_message("error", "adminLogout fail, _logoutAccount fail. $tmpLogEntity");

                return false;
            }
            
            $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGOUT, $entityLoginAccount);

        }
        else {
            //logout all account
            foreach ($collectionLoginAccount->result as $entityLoginAccount) {
                if (!$this->_logoutAccount($entityLoginAccount)) {
                    return false;
                }
                
                $this->_addLoginLog(LoginLogStatus::SUCCESS, LoginLogType::LOGOUT, $entityLoginAccount);
            }
        }

        $this->setResponseCode(MessageCode::CODE_LOGOUT_SUCCESS);
        return true;
    }

    public function getAccessibleMenuList($user_profile_id) {
        $serviceUserFun = $this->_getServiceUserFun();
        $collectionFun = $serviceUserFun->selectByUserId($user_profile_id);
        if ($collectionFun) {
            // $this->setResponseCode(MessageCode::CODE_ACCESS_MENU_LIST_FAILED);
            // return false;

            $collectionFun = $collectionFun->result->getSelectedField(array('id', 'fun' =>array('id', 'code', 'name', 'display_type', 'display_order')));
        }else{
            $collectionFun = array();
        }


        

        

        $servFun = FunService::build();
        $filter = new Fun();
        $filter->setDisplayType(FunType::MENU);
        // $isRoot = true;
        if($menuList = $servFun->selectFun($filter, 'display_order asc')){
            
            $filter = new Fun();
            $allList = $servFun->selectFun($filter, 'display_order asc');
            
            $arrResult = array();
            foreach ($menuList->result as $entityFun) {
                $arrFun = $entityFun->getSelectedField(array('id','code','name','display_type','parent_id'));
                
                
                $menuId   = $entityFun->getId();
                $menuName = $entityFun->getName();

                $arrFun['access'] = false;
                foreach ($collectionFun as $key => $value) {
                    $funId = $value['fun']['id'];

                    if($funId == $menuId){
                        $arrFun['access'] = true;
                        break;
                    }
                }

                
                $filter = new Fun();
                $filter->setParentId($menuId);
                
                if($funList = $servFun->selectFun($filter, 'display_order asc')){
                    $childList = array();
                    foreach ($funList->result as $entityFun) {
                        $childArr                = $entityFun->getSelectedField(array('id','code','name','display_type','parent_id'));
                        $childArr['parent_name'] = $menuName;
                        $childId                 = $entityFun->getId();
                        $childName               = $entityFun->getName();
                        

                        $childArr['access'] = false;
                        foreach ($collectionFun as $key => $v) {
                            $funId = $v['fun']['id'];

                            if($funId == $childId){
                                $childArr['access'] = true;
                                break;
                            }
                        }
                        $childList[]             = $childArr;
                    }

                    $arrFun['child'] = $childList;
                }

                $arrResult[] = $arrFun;
            }
            $menuList->result = $arrResult;
        }

        $this->setResponseCode(MessageCode::CODE_ACCESS_MENU_LIST_SUCCESS);
        return $menuList;
    }

//     public function getAccessibleModuleList($user_profile_id) {
//         $serviceUserFun = $this->_getServiceUserFun();
//         $collectionAll = $serviceUserRole->getFunctionListByUserProfileId($user_profile_id);
//         if (!$collectionAll) {
//             $this->setResponseCode(MessageCode::CODE_ACCESS_MENU_LIST_FAILED);
//             return false;
//         }

//         $collectionMenu = array();
//         foreach ($collectionAll as $entityFun) {
//             if ($entityFun->getParentId() == null && $entityFun->getDisplayType() == FunType::MENU) {

//                 $collection = clone $collectionAll;
//                 $collection->rewind();

//                 $id = $entityFun->getId();
//                 $code = $entityFun->getCode();
//                 $arrFun = $entityFun->getSelectedField(array('id', 'code', 'name', 'display_type', 'description', 'url', 'parent_id'));
//                 $arrFun['parent_code'] = null;
//                 $arrFun['child'] = null;
//                 if ($child = $this->getChildModuleList($id, $code, $collection)) {
//                     $arrFun['child'] = $child;
//                 }
//                 $collectionMenu[] = $arrFun;
//             }
//         }

// //        $collectionMenu = $collectionAll->getGroupByModuleList();

//         $this->setResponseCode(MessageCode::CODE_ACCESS_MENU_LIST_SUCCESS);
//         return $collectionMenu;
//     }

    // private function getChildModuleList($parent_id, $parent_code, $collectionAll) {

    //     $arrResult = array();
    //     foreach ($collectionAll as $entityFun) {
    //         if ($entityFun->getParentId() == $parent_id) {

    //             $collection = clone $collectionAll;
    //             $collection->rewind();

    //             $id = $entityFun->getId();
    //             $code = $entityFun->getCode();
    //             $arrFun = $entityFun->getSelectedField(array('id', 'code', 'name', 'display_type', 'description', 'url', 'parent_id'));
    //             $arrFun['parent_code'] = $parent_code;
    //             $arrFun['child'] = null;
    //             if ($child = $this->getChildModuleList($id, $code, $collection)) {
    //                 $arrFun['child'] = $child;
    //             }
    //             $arrResult[] = $arrFun;
    //         }
    //     }
    //     if (count($arrResult) > 0) {
    //         return $arrResult;
    //     }
    //     return null;
    // }

    public function getAdminUserList($username, UserProfile $entityUserProfile, $orderBy, $limit, $page) {

        //find role
        // $serviceRole = $this->_getServiceRole();
        // if (!$collectionRole = $serviceRole->findByCodeArr($roleCode)) {
        //     log_message("error", "createAdminUser[failed] - admin role not found - $roleCode");
        //     $this->setResponseCode($serviceRole->getResponseCode());
        //     return false;
        // }

        $serviceLoginAccount = $this->_getServiceLoginAccount();

        if ($username) {
            if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserName($username)) {
                $this->setResponseCode(MessageCode::CODE_ADMIN_USER_NOT_FOUND);
                //user not found.
                return false;
            }
            $entityUserProfile->setId($entityLoginAccount->getUserId());
        }
        
        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceLoginLog = $this->_getServiceLoginLog();
        if ($collection = $serviceUserProfile->selectAdminUserList($entityUserProfile, $orderBy, $limit, $page)) {

            $user_ids = $collection->result->getFieldValues('id');

            $filterLoginAccount = new LoginAccount();
            $filterLoginAccount->setUserId($user_ids);
            $filterLoginAccount->setLoginType(LoginAccountLoginType::USERNAME);

            $collectionLoginAccount = $serviceLoginAccount->selectLoginAccount($filterLoginAccount);

            $result = array();
            foreach ($collection->result as $entityUserProfile) {

                $arrUser = $entityUserProfile->getSelectedField(array('id', 'accountID', 'name', 'status', 'gender', 'avatar_url', 'created_at', 'created_by_name', 'last_login_at'));
                $arrUser["username"] = null;
                $id = $entityUserProfile->getId();
                if ($collectionLoginAccount) {
                    if ($entityLoginAccount = $collectionLoginAccount->result->getFirstByFieldValue("user_id", $id)) {
                        $arrUser["username"] = $entityLoginAccount->getUserName();
                    }
                }
                
                $arrUser["last_login_ip"] = null;
                $filterLoginLog = new LoginLog();
                $filterLoginLog->setUserId($id);
                if($collectionLoginLog = $serviceLoginLog->selectLoginLog($filterLoginLog, null, 1, 1)){
                    $entityLoginLog = $collectionLoginLog->result->current();
                    $arrUser["last_login_ip"] = $entityLoginLog->getIpAddress()->getString();
                }

                $result[] = $arrUser;
            }
            $collection->result = $result;

            $this->setResponseCode(MessageCode::CODE_ADMIN_USER_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_ADMIN_USER_NOT_FOUND);
        return false;
    }

    public function createAdminUser($username, $password, $name, $status, $login_type = LoginAccountLoginType::USERNAME) {
        return $this->createUser(UserType::ADMIN, $username, $login_type, $status, $password, $name, $name);
    }

    public function getAdminUser($id) {

        $filterUserProfile = new UserProfile();
        $filterUserProfile->setId($id);
        $serviceUserProfile = $this->_getServiceUserProfile();
        if ($collection = $serviceUserProfile->selectAdminUserList($filterUserProfile)) {

            $entityUserProfile = $collection->result->current();
            $id = $entityUserProfile->getId();

            $arrUser = $entityUserProfile->getSelectedField(array('id', 'accountID', 'name', 'user_status', 'gender', 'avatar_url', 'created_at', 'created_by_name'));
            $arrUser["username"] = null;
            
            $serviceLoginAccount = $this->_getServiceLoginAccount();
            $filterLoginAccount = new LoginAccount();
            $filterLoginAccount->setUserId($id);
            if ($collectionLoginAccount = $serviceLoginAccount->selectLoginAccount($filterLoginAccount)) {
                $entityLoginAccount = $collectionLoginAccount->result->current();
                $arrUser["username"] = $entityLoginAccount->getUserName();
            }

            $access            = $this->getAccessibleMenuList($id);

            // var_dump($access);exit();
            $arrUser['access'] = $access->result;

            $this->setResponseCode(MessageCode::CODE_ADMIN_USER_GET_SUCCESS);
            return $arrUser;
        }

        $this->setResponseCode(MessageCode::CODE_ADMIN_USER_NOT_FOUND);
        return false;
    }

    public function editAdminUser($id, $username, $password, $name, $userFun, $status, $login_type = LoginAccountLoginType::USERNAME) {

        //check login account if exists
        $serviceLoginAccount = $this->_getServiceLoginAccount();

        if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($id, $login_type)) {
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
            return false;
        }

        if ($entityLoginAccountExisting = $serviceLoginAccount->getLoginAccountByUserName($username, $login_type)) {
            if ($entityLoginAccountExisting->getUserId() != $id) {
                $this->setResponseCode(MessageCode::CODE_ADMIN_ALREADY_EXISTING);
                return false;
            }
        }

        //update user profile
        $entityUserProfile = new UserProfile();
        $entityUserProfile->setId($id);
        $entityUserProfile->setStatus($status);
        $entityUserProfile->setName($name);
        $entityUserProfile->setUpdatedBy($this->getUpdatedBy());
        $serviceUserProfile = $this->_getServiceUserProfile();

        //start db transaction
        $serviceUserProfile->startDBTransaction();

        //insert user profile
        if (!$entityUserProfileUpdated = $serviceUserProfile->updateUserProfile($entityUserProfile)) {

            //rollback db transaction
            $serviceUserProfile->rollbackDBTransaction();

            log_message("error", "editAdminUser[failed] - admin profile update failed - " . json_encode($entityUserProfile));
            $this->setResponseCode($serviceUserProfile->getResponseCode());
            return false;
        }

        //update login account
        $entityLoginAccount->setUsername($username);
        if (!empty($password)) {
            $passwordObj = new PasswordObj();
            $passwordObj->setNewPassword($password);
            $entityLoginAccount->setPassword($passwordObj);
        }
        $entityLoginAccount->setUpdatedBy($this->getUpdatedBy());

        if (!$entityLoginAccountUpdated = $serviceLoginAccount->updateLoginAccount($entityLoginAccount)) {

            //rollback transaction
            $serviceUserProfile->rollbackDBTransaction();

            log_message("error", "editAdminUser[failed] - admin login account update failed - " . json_encode($entityLoginAccount));
            $this->setResponseCode($serviceUserProfile->getResponseCode());
            return false;
        }


        $servUserFun = $this->_getServiceUserFun();
        //delete user fun
        if (!$servUserFun->deleteByUserIdAndFun($id)) {
            //rollback transaction
            $serviceUserProfile->rollbackDBTransaction();
            $this->setResponseCode($servUserFun->getResponseCode());
            return false;
        }

        if(!empty($userFun)){

            foreach ($userFun as $key => $value) {
                $funId = $value;

                $userFunEntity = new UserFun();
                $userFunEntity->setFunId($funId);
                $userFunEntity->setUserId($id);

                //add user fun
                if (!$servUserFun->addUserFun($userFunEntity)) {

                    //rollback transaction
                    $serviceUserProfile->rollbackDBTransaction();
                    $this->setResponseCode($servUserFun->getResponseCode());
                    return false;
                }
            }
        }
        

        //complete transaction
        $serviceUserProfile->completeDBTransaction();

        $this->setResponseCode(MessageCode::CODE_ADMIN_USER_UPDATE_SUCCESS);
        return array($entityLoginAccount, $password);
    }

    public function deleteAdminUser($id) {

        $serviceUserProfile = $this->_getServiceUserProfile();
        $serviceLoginAccount = $this->_getServiceLoginAccount();

        //start db transaction
        $serviceUserProfile->startDBTransaction();

        //delete login account
        $filterLoginAccount = new LoginAccount();
        $filterLoginAccount->setUserId($id);
        if (!$collectionLoginAccount = $serviceLoginAccount->selectLoginAccount($filterLoginAccount)) {

            //rollback db transaction
            $serviceUserProfile->rollbackDBTransaction();

            log_message("error", "deleteAdminUser[failed] - admin login account not found - $id");
            $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
            return false;
        }

        //delete access token
        $login_account_ids = $collectionLoginAccount->result->getFieldValues('id');

        $serviceAccessToken = $this->_getServiceAccessToken();
        if ($collectionAccessToken = $serviceAccessToken->getByLoginAccountIDs($login_account_ids)) {
            foreach ($collectionAccessToken->result as $entityAccessToken) {
                if (!$serviceAccessToken->deleteAccessToken($entityAccessToken->getId())) {

                    //rollback db transaction
                    $serviceUserProfile->rollbackDBTransaction();

                    log_message("error", "deleteAdminUser[failed] - admin access token delete fail - " . json_encode($entityAccessToken));
                    $this->setResponseCode(MessageCode::CODE_ADMIN_USER_DELETE_FAIL);
                    return false;
                }
            }
        }

        foreach ($collectionLoginAccount->result as $entityLoginAccount) {
            if (!$serviceLoginAccount->deleteLoginAccount($entityLoginAccount->getId())) {

                //rollback transaction
                $serviceUserProfile->rollbackDBTransaction();

                log_message("error", "deleteAdminUser[failed] - admin login account delete fail - " . json_encode($entityLoginAccount));
                $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_DELETE_FAIL);
                return false;
            }
        }

        $servUserFun = $this->_getServiceUserFun();
        //delete user fun
        if (!$servUserFun->deleteByUserIdAndFun($id)) {
            //rollback transaction
            $serviceUserProfile->rollbackDBTransaction();
            $this->setResponseCode($servUserFun->getResponseCode());
            return false;
        }

        //delete user profile
        if (!$serviceUserProfile->deleteUserProfile($id)) {

            //rollback db transaction
            $serviceUserProfile->rollbackDBTransaction();

            log_message("error", "editAdminUser[failed] - admin profile delete failed - " . json_encode($entityUserProfile));
            $this->setResponseCode($serviceUserProfile->getResponseCode());
            return false;
        }


        //complete transaction
        $serviceUserProfile->completeDBTransaction();

        $this->setResponseCode(MessageCode::CODE_ADMIN_USER_DELETE_SUCCESS);
        return true;
    }


    /////////////////////////////////////////////////////转客用户管理////////////////////////////////////////////////////////////////

    public function createAppUser($username, $password, $nickName = NULL) {

        return $this->createUser(UserType::APPUSER, $username, LoginAccountLoginType::MOBILE, UserStatus::VERIFIED, $password, null, $nickName);
    }

    public function getUserListByAdmin(UserProfile $filterUserProfile, $parent, $orderBy, $limit = 20, $page = 1){
        $serviceUserProfile = $this->_getServiceUserProfile();

        if(!$result = $serviceUserProfile->getRepository()->getAppUserListByAdmin($filterUserProfile, $parent, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_ADMIN_USER_NOT_FOUND);
            return false;
        }


        // foreach ($result['result'] as $key => $value) {
        //     $ipaddress = $value->ip_address;

        //     $loginIpAddress = $value->login_ip;

        //     // if(!empty($ipaddress)){
        //     //     $ipString = IpAddress::fromInt($ipaddress)->getString();
        //     //     // $address = IpAddress::getAddressByString($ipString);
        //     //     // $value->address = $address;
        //     //     $value->ip_address = $ipString;
        //     // }else{
        //         $ipString = IpAddress::fromInt($loginIpAddress)->getString();
        //         // $address = IpAddress::getAddressByString($ipString);
        //         // $value->address = $address;
        //         $value->ip_address = $ipString;
        //     // }
        // }

        $servUserInvite = $this->_getServiceUserInvite();
        $servLoginLog = $this->_getServiceLoginLog();
        foreach ($result['result'] as $key => &$value) {
            $userId = $value->id;

            $loginLogFilter = new LoginLog();
            $loginLogFilter->setUserId($userId);


            $value->ip_address = null;
            $value->address = null;
            $value->last_login_at = null;
            if($collection = $servLoginLog->selectLoginLog($loginLogFilter, 'id desc', 1,1)){
                $loginLog = $collection->result->current();


                if(!empty($loginLog->getIpAddress())){
                    $value->ip_address = $loginLog->getIpAddress()->getString();
                }
                
                $value->address = $loginLog->getAddress();

                if(!empty($loginLog->getCreatedAt())){
                    $value->last_login_at = $loginLog->getCreatedAt()->getString();
                }
            }


            //获取有效徒弟、有效徒孙数量
            $count1 = $servUserInvite->countValidFriend($userId, 1);
            $count2 = $servUserInvite->countValidFriend($userId, 2);

            $value->level_1_valid = $count1->count;
            $value->level_2_valid = $count2->count;
        }

        $this->setResponseCode(MessageCode::CODE_ADMIN_USER_GET_SUCCESS);
        return $result;
    }

    public function getAppUserRelationship($parentId, $level, $orderBy, $limit, $page){
        $serviceUserProfile = $this->_getServiceUserProfile();

        if(!$result = $serviceUserProfile->getRepository()->getAppUserRelationship($parentId, $level, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_ADMIN_USER_NOT_FOUND);
            return false;
        }


        foreach ($result['result'] as $key => $value) {
            $ipaddress = $value->ip_address;

            $loginIpAddress = $value->login_ip;

            if(!empty($ipaddress)){
                $ipString = IpAddress::fromInt($ipaddress)->getString();
                // $address = IpAddress::getAddressByString($ipString);
                // $value->address = $address;
                $value->ip_address = $ipString;
            }else{
                $ipString = IpAddress::fromInt($loginIpAddress)->getString();
                // $address = IpAddress::getAddressByString($ipString);
                // $value->address = $address;
                $value->ip_address = $ipString;
            }
        }

        $this->setResponseCode(MessageCode::CODE_ADMIN_USER_GET_SUCCESS);
        return $result;
    }

    public function editAppUser($id, $password, $name, $status, $userGroupId, $remark) {

        $serviceUserProfile  = $this->_getServiceUserProfile();
        if(!$userProfileEntity = $serviceUserProfile->getUserProfile($id)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
            return false;   
        }

        if(!empty($userGroupId) && $userGroupId != $userProfileEntity->getUserGroupId()){
            //用户原来的分组和现在的分组不一致，需要重新计算核减
            $currentDate = date("Y-m-d");
            $servUserDailyStatics = UserDailyStaticsService::build();
            $servUserDailyStatics->clearDeduNum($id, $currentDate);
        }


        $serviceLoginAccount = $this->_getServiceLoginAccount();

        //update user profile
        $entityUserProfile = new UserProfile();
        $entityUserProfile->setId($id);

        if(!empty($status)){
            $entityUserProfile->setStatus($status);
        }
        
        $entityUserProfile->setName($name);
        $entityUserProfile->setUserGroupId($userGroupId);
        $entityUserProfile->setRemark($remark);
        $entityUserProfile->setUpdatedBy($this->getUpdatedBy());
        

        //start db transaction
        $serviceUserProfile->startDBTransaction();

        if (!$serviceUserProfile->updateUserProfile($entityUserProfile)) {

            //rollback db transaction
            $serviceUserProfile->rollbackDBTransaction();

            log_message("error", "edit app user profile failed - " . json_encode($entityUserProfile));
            $this->setResponseCode($serviceUserProfile->getResponseCode());
            return false;
        }

        //update login account
        if (!empty($password)) {

            //判断是否存在手机号登录方式, 如果不存在，返回错误
            if (!$entityLoginAccount = $serviceLoginAccount->getLoginAccountByUserId($id, LoginAccountLoginType::MOBILE)) {
                $this->setResponseCode(MessageCode::CODE_LOGIN_ACCOUNT_NOT_FOUND);
                return false;
            }

            $passwordObj = new PasswordObj();
            $passwordObj->setNewPassword($password);
            $entityLoginAccount->setPassword($passwordObj);

            $entityLoginAccount->setUpdatedBy($this->getUpdatedBy());

            if (!$serviceLoginAccount->updateLoginAccount($entityLoginAccount)) {

                //rollback transaction
                $serviceUserProfile->rollbackDBTransaction();

                log_message("error", "edit app user login account failed - " . json_encode($entityLoginAccount));
                $this->setResponseCode($serviceUserProfile->getResponseCode());
                return false;
            }

        }
        
        //complete transaction
        $serviceUserProfile->completeDBTransaction();

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_SUCCESS);
        return true;
    }



    public function updateUserGroup($id, $userGroupId, $status){
        $serviceUserProfile  = $this->_getServiceUserProfile();

        if(empty($id)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_FAIL);
            return false;
        }

        if(empty($userGroupId) && empty($status)){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_FAIL);
            return false;
        }

        $res = NULL;
        if(is_array($id)){

            if(!empty($userGroupId)){
                $servUserDailyStatics = UserDailyStaticsService::build();
                foreach ($id as $key => $value) {
                    $uId = $value;
                    if(empty($uId)){
                        continue;
                    }

                    if(!$userProfileEntity = $serviceUserProfile->getUserProfile($uId)){
                        continue;
                    }

                    if($userGroupId != $userProfileEntity->getUserGroupId()){
                        //用户原来的分组和现在的分组不一致，需要重新计算核减
                        $currentDate = date("Y-m-d");
                        $servUserDailyStatics->clearDeduNum($uId, $currentDate);
                    }

                }
                
                $res = $serviceUserProfile->updateUserGroupByIds($id, $userGroupId);            
            }

            if(!empty($status)){
                $res = $serviceUserProfile->updateUserStatusByIds($id, $status);
            }
        }else{
            
            if(!empty($userGroupId)){

                if(!$userProfileEntity = $serviceUserProfile->getUserProfile($id)){
                    $this->setResponseCode(MessageCode::CODE_USER_PROFILE_NOT_FOUND);
                    return false;   
                }

                if($userGroupId != $userProfileEntity->getUserGroupId()){
                    //用户原来的分组和现在的分组不一致，需要重新计算核减
                    $currentDate = date("Y-m-d");
                    $servUserDailyStatics = UserDailyStaticsService::build();
                    $servUserDailyStatics->clearDeduNum($id, $currentDate);
                }


                $res = $serviceUserProfile->updateUserGroup($id, $userGroupId);                
            }

            if(!empty($status)){
                $res = $serviceUserProfile->updateUserStatus($id, $status);
            }
        }

        if(!$res){
            $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_FAIL);
            return false;
        }

        $this->setResponseCode(MessageCode::CODE_USER_PROFILE_UPDATE_SUCCESS);
        return true;
    }


    /**
     * 绑定邀请码
     * @param type $user_id
     * @param type $invite_code
     * @return boolean
     */
    public function bindInviteCode($userId, $inviteCode){
        
        //给新用户绑定师傅
        $servUserInvite = $this->_getServiceUserInvite();
        
        if(!$userInvite = $servUserInvite->checkExistsByUserId($userId)){
            $this->setResponseCode($servUserInvite->getResponseCode());
            return false;
        }

        //新用户是否有师傅
        if(!empty($userInvite->getParent1()) || !empty($userInvite->getParent2())){
            //已经绑定的有师傅或师祖
            $this->setResponseCode(MessageCode::CODE_USER_ALREADY_HAS_MASTER);
            return false;
        }

        $userInviteFilter = new UserInvite();
        $userInviteFilter->setParent1($userId);

        //新用户是否有徒弟
        if($servUserInvite->selectUserInvite($userInviteFilter, null, 1,1)){
            $this->setResponseCode(MessageCode::CODE_USER_HAS_FRIEND_CANNOT_BIND);
            return false;
        }

        //邀请码是否存在
        if(!$entityUserInviteParent = $servUserInvite->checkExistsByCode($inviteCode,$userId)){
            $this->setResponseCode($servUserInvite->getResponseCode());
            return false;
        }

        $parent1UserId = $entityUserInviteParent->getUserId();
        $parent2UserId = $entityUserInviteParent->getParent1();
        $rootUserId    = $entityUserInviteParent->getRootUserId();
        $rareKeyType   = $entityUserInviteParent->getRareKeyType();


        
        //更新新用户的 user_invite里的 parent1, parent2,root_user_id,rare_key_type
        $userInviteEntity = new UserInvite();
        $userInviteEntity->setId($userInvite->getId());
        $userInviteEntity->setParent1($parent1UserId);

        if(!empty($parent2UserId)){
            $userInviteEntity->setParent2($parent2UserId);
        }

        if(!empty($rootUserId)){
            $userInviteEntity->setRootUserId($rootUserId);
        }

        if(!empty($rareKeyType)){
            $userInviteEntity->setRareKeyType($rareKeyType);
        }

        if(!$servUserInvite->updateUserInvite($userInviteEntity)){
            $this->setResponseCode($servUserInvite->getResponseCode());
            return false;
        }

        $servDataStatics = $this->_getServiceDataStatics();
        $date = date('Y-m-d');
        $hour = date('H');
        //更新上级用户统计信息(邀请人数+1)
        if(!empty($parent1UserId)){
            $nums = array("invite_num" => 1, "invite_num1" => 1);
            $servDataStatics->updateUserDailyStaticsNum($parent1UserId, $date, $hour, $nums);
        }
        
        if(!empty($parent2UserId)){
            //更新上上级用户统计信息(邀请人数+1)
            $nums = array("invite_num" => 1, "invite_num2" => 1);
            $servDataStatics->updateUserDailyStaticsNum($parent2UserId, $date, $hour, $nums);
        }
        
        $this->setResponseCode(MessageCode::CODE_USER_INVITE_UPDATE_SUCCESS);
        return true;
    }
}
