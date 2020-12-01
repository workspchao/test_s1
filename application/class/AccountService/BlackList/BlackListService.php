<?php

namespace AccountService\BlackList;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use AccountService\LoginLog\LoginLog;
use AccountService\LoginLog\LoginLogCollection;
use AccountService\LoginLog\LoginLogStatus;
use AccountService\LoginLog\LoginLogService;
use AccountService\CoreConfigData\CoreConfigDataService;
use AccountService\CoreConfigData\CoreConfigType;
use Common\Core\IpAddress;

class BlackListService extends BaseService {

    protected static $_instance = NULL;
    
    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('blacklist/Black_list_model');
            self::$_instance = new BlackListService($_ci->Black_list_model);
        }
        return self::$_instance;
    }

    public function addBlackList(BlackList $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_BLACK_LIST_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_BLACK_LIST_ADD_FAIL);
        return false;
    }

    public function deleteBlackList($id, $isLogic = true) {

        $filter = new BlackList();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_BLACK_LIST_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_BLACK_LIST_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_BLACK_LIST_DELETE_FAIL);
        return false;
    }

    public function updateBlackList(BlackList $entity) {

        $filter = new BlackList();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_BLACK_LIST_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_BLACK_LIST_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_BLACK_LIST_UPDATE_FAIL);
        return false;
    }

    public function selectBlackList(BlackList $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_BLACK_LIST_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_BLACK_LIST_NOT_FOUND);
        return false;
    }

    public function getBlackList($id) {

        if($entity = $this->getRepository()->getById($id)){
            $this->setResponseCode(MessageCode::CODE_BLACK_LIST_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_BLACK_LIST_NOT_FOUND);
        return false;
    }

    public function getLastRecordByIp(IpAddress $ip_address, BaseDateTime $fromDate = null){
        $filter = new BlackList();
        //$filter->setType(BlackListType::IP);
        $filter->setIpAddress($ip_address);
        if($fromDate)
            $filter->setCreatedFrom ($fromDate);
        
        if($collection = $this->selectBlackList($filter, "created_at desc", 1, 1)){
            return $collection->result->current();
        }
        return null;
    }
    
    public function getLastRecordByUserId($user_id, BaseDateTime $fromDate = null){
        $filter = new BlackList();
        //$filter->setType(BlackListType::USER);
        $filter->setUserId($user_id);
        if($fromDate)
            $filter->setCreatedFrom ($fromDate);
        
        if($collection = $this->selectBlackList($filter, "created_at desc", 1, 1)){
            return $collection->result->current();
        }
        return null;
    }
    
    public function checkInBlackList(IpAddress $ipAddress, $user_profile_id = NULL){
        $this->_checkByIpAddress($ipAddress);
        if(!empty($user_profile_id)){
            $this->_checkByUserId($user_profile_id);
        }
        return true;
    }

    public function add(IpAddress $ipAddress = NULL, $user_profile_id = NULL, $level = BlackListLevel::LOGIN) {
        $entityBlackList = new BlackList();
        $entityBlackList->setLevel($level);
        if (!is_null($ipAddress)) {
            $entityBlackList->setType(BlackListType::IP);
            $entityBlackList->setIpAddress($ipAddress);
        }
        elseif (!is_null($user_profile_id)) {
            $entityBlackList->setType(BlackListType::USER);
            $entityBlackList->setUserProfileId($user_profile_id);
        }
        else
            return false;

        $entityBlackList->setCreatedBy($this->getUpdatedBy());
        $entityBlackList->setStatus(BlackListStatus::ACTIVE);

        return $this->addBlackList($entityBlackList);
    }

    public function release($blacklist_id, $is_auto = false, $remark = NULL) {
        $filter = new BlackList();
        $filter->setId($blacklist_id);

        if ($collection = $this->selectBlackList($filter)) {
            $blacklist = $collection->result->current();
            if ($blacklist instanceof BlackList) {
                if ($blacklist->getStatus() == BlackListStatus::ACTIVE) {
                    if ($is_auto)
                        $blacklist->setStatus(BlackListStatus::AUTO_RELEASED);
                    else
                        $blacklist->setStatus(BlackListStatus::MANUAL_RELEASED);

                    $blacklist->setReleasedBy($this->getUpdatedBy());
                    $blacklist->setReleasedAt(BaseDateTime::now());
                    $blacklist->setUpdatedBy($this->getUpdatedBy());
                    $blacklist->setRemarks($remark);

                    if ($this->updateBlacklist($blacklist)) {
                        $this->setResponseCode(MessageCode::CODE_BLACK_LIST_RELEASE_SUCCESS);
                        return $blacklist;
                    }
                }
            }
        }

        $this->setResponseCode(MessageCode::CODE_BLACK_LIST_RELEASE_FAILED);
        return false;
    }

    public function screen(IpAddress $ipAddress = NULL, $user_profile_id = NULL) {
        $configServ = CoreConfigDataService::build();

        //screen by ip address
        if (!is_null($ipAddress)) {
            if ($blacklist = $this->getLastRecordByIp($ipAddress) AND
                    $blacklist instanceof BlackList) {
                if ($blacklist->getStatus() == BlackListStatus::ACTIVE) {//check if lock from created time exceeded the release period, if yes, release the lock
                    //auto release if exceeded max period
                    if (!$max_period = $configServ->getConfig(CoreConfigType::MAX_CONSECUTIVE_LOGIN_PER_IP_PERIOD))
                        $max_period = 30;

                    if (!$this->_eligibleToAutoRelease($blacklist, $max_period))
                        return false; //this guy is still blacklisted                    
                }
            }
        }

        if (!is_null($user_profile_id)) {
            if ($blacklist = $this->getLastRecordByUserId($user_profile_id) AND
                    $blacklist instanceof BlackList) {
                if ($blacklist->getStatus() == BlackListStatus::ACTIVE) {//check if lock from created time exceeded the release period, if yes, release the lock
                    //auto release if exceeded max period
                    if (!$max_period = $configServ->getConfig(CoreConfigType::MAX_CONSECUTIVE_LOGIN_PER_USER_PERIOD))
                        $max_period = 30;

                    if (!$this->_eligibleToAutoRelease($blacklist, $max_period))
                        return false; //this guy is still blacklisted
                }
            }
        }

        //return ok if no active lock is found
        return true; //this guy is free
    }

    protected function _checkByIpAddress(IpAddress $ipAddress) {
        
        $serviceCoreConfigData = CoreConfigDataService::build();
        $serviceLoginLog = LoginLogService::build();

        //default 30 minutes (get login logs from before 30 minutes)
        $fromDate = BaseDateTime::now()->subMinute(30);
        if ($max_period = $serviceCoreConfigData->getConfig(CoreConfigType::MAX_CONSECUTIVE_LOGIN_PER_IP_PERIOD))
            $fromDate = BaseDateTime::now()->subMinute($max_period);

        //get last blacklist record
        if ($entityBlackList = $this->getLastRecordByIp($ipAddress)) {
            if ($entityBlackList->getStatus() == BlacklistStatus::ACTIVE){
                //no further checking needed
                return false;
            }

            if (!$entityBlackList->getReleasedAt()->isNull())
                $tempDate = $entityBlackList->getReleasedAt();
            else
                $tempDate = $entityBlackList->getCreatedAt();

            if ($tempDate->getUnix() > $fromDate->getUnix())   //if tempDate is nearer from now
                $fromDate = $tempDate;
        }

        if (!$max_attempt = $serviceCoreConfigData->getConfig(CoreConfigType::MAX_CONSECUTIVE_LOGIN_PER_IP))
            $max_attempt = 5; //default

        $filter = new LoginLog();
        $filter->setIpAddress($ipAddress);
        $filter->setCreatedFrom($fromDate);
        
        if ($collectionLoginLog = $serviceLoginLog->selectLoginLog($filter, "created_at desc", $max_attempt, 1)) {
            $collectionLoginLog = $collectionLoginLog->result;
            if ($collectionLoginLog instanceof LoginLogCollection) {
                if ($this->_checkBlacklistRequiredByLoginLogs($collectionLoginLog, $max_attempt)) {
                    
                    //add ip black list
                    log_message("info", "Blacklist: adding ip to blacklist record " . $ipAddress->getString());
                    // $this->add($ipAddress);
                }
            }
        }

        return true;
    }

    protected function _checkByUserId($user_profile_id) {
        
        $serviceCoreConfigData = CoreConfigDataService::build();
        $serviceLoginLog = LoginLogService::build();

        //default 30 minutes (get login logs from before 30 minutes)
        $fromDate = BaseDateTime::now()->subMinute(30);
        if ($max_period = $serviceCoreConfigData->getConfig(CoreConfigType::MAX_CONSECUTIVE_LOGIN_PER_USER_PERIOD))
            $fromDate = BaseDateTime::now()->subMinute($max_period);

        //get last blacklist record
        if ($entityBlackList = $this->getRepository()->getLastRecordByUserId($user_profile_id)) {
            if ($entityBlackList->getStatus() == BlackListStatus::ACTIVE){
                //no further checking needed
                return false;
            }
            
            if (!$entityBlackList->getReleasedAt()->isNull())
                $tempDate = $entityBlackList->getReleasedAt();
            else
                $tempDate = $entityBlackList->getCreatedAt();

            if ($tempDate->getUnix() > $fromDate->getUnix())   //if tempDate is nearer from now
                $fromDate = $tempDate;
        }

        if (!$max_attempt = $serviceCoreConfigData->getConfig(CoreConfigType::MAX_CONSECUTIVE_LOGIN_PER_USER))
            $max_attempt = 5; //default

        
        $filter = new LoginLog();
        $filter->setUserId($user_profile_id);
        $filter->setCreatedFrom($fromDate);
        
        if ($col = $loginLogServ->selectLoginLog($filterLoginLog, "created_at desc", $max_attempt, 1)) {
            $col = $col->result;
            if ($col instanceof LoginLogCollection) {
                if ($this->_checkBlacklistRequiredByLoginLogs($col, $max_attempt)) {
                    $this->add(NULL, $user_profile_id);
                }
            }
        }

        return true;
    }

    protected function _checkBlacklistRequiredByLoginLogs(LoginLogCollection $logs, $max_allowed_attempt) {
        $attempt = 0;
        foreach ($logs AS $log) {
            if ($log instanceof LoginLog) {
                if ($log->getStatus() == LoginLogStatus::FAILED)
                    $attempt += 1;
                else
                    break;

                if ($attempt >= $max_allowed_attempt) {
                    //insert blacklist record
                    return true;
                }
            }
        }

        return false;
    }

    protected function _eligibleToAutoRelease(BlackList $blacklist, $max_period) {
        if ($blacklist->getCreatedAt()->getUnix() <= BaseDateTime::now()->subMinute($max_period)->getUnix()) {
            //release
            if ($this->release($blacklist->getId(), true))
                return true;
        }

        //this guy is still blacklisted
        return false;
    }

}
