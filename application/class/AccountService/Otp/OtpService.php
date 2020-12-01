<?php

namespace AccountService\Otp;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use Common\Core\BaseEntity;
use AccountService\UserProfile\UserProfile;
use AccountService\Otp\OtpType;
use AccountService\CoreConfigData\CoreConfigDataService;
use AccountService\CoreConfigData\CoreConfigType;
use AccountService\Sms\SmsService;

class OtpService extends BaseService {

    protected static $_instance = NULL;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('otp/Otp_model');
            self::$_instance = new OtpService($_ci->Otp_model);
        }
        return self::$_instance;
    }

    public function addOtp(Otp $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if ($entity = $this->getRepository()->insert($entity)) {
            $this->setResponseCode(MessageCode::CODE_OTP_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_OTP_ADD_FAIL);
        return false;
    }

    public function deleteOtp($id, $isLogic = true) {

        $filter = new Otp();
        $filter->setId($id);

        $oldEntity = null;
        if ($collection = $this->getRepository()->select($filter)) {
            $oldEntity = $collection->result->current();
        }

        if (!$oldEntity) {
            $this->setResponseCode(MessageCode::CODE_OTP_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if ($this->getRepository()->delete($oldEntity, $isLogic)) {
            $this->setResponseCode(MessageCode::CODE_OTP_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_OTP_DELETE_FAIL);
        return false;
    }

    public function updateOtp(Otp $entity) {

        $filter = new Otp();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if ($collection = $this->getRepository()->select($filter)) {
            $oldEntity = $collection->result->current();
        }

        if (!$oldEntity) {
            $this->setResponseCode(MessageCode::CODE_OTP_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if ($entity = $this->getRepository()->update($entity)) {
            $this->setResponseCode(MessageCode::CODE_OTP_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_OTP_UPDATE_FAIL);
        return false;
    }

    public function selectOtp(Otp $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if ($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)) {
            $this->setResponseCode(MessageCode::CODE_OTP_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_OTP_NOT_FOUND);
        return false;
    }

    public function getOtp($id) {

        if ($entity = $this->getRepository()->getById($id)) {
            $this->setResponseCode(MessageCode::CODE_OTP_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_OTP_NOT_FOUND);
        return false;
    }

    #========== ========== ========== ========== ==========#

    /**
     * generate and send otp code
     * this is to generate otp and fire notification event
     * @param type $otpType
     * @param type $destination
     * @param type $entityUser
     * @return boolean
     */
    public function generateOtp($otpType, $destination, $entityUser = null) {
        
        if (!$entityOtp = $this->_buildOtpCode($otpType, $destination, $entityUser)) {
            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;    
        }
        
        $activeOtp = null;
        $userId    = null;
        if($entityUser != null){
            $userId = $entityUser->getId();
        }

        //检测频繁操作
        if(!$this->checkFrequentAction($userId, $destination, $this->getIpAddress())){
            $this->setResponseCode(MessageCode::CODE_FREQUENT_ACTION);
            return false;
        }

        //同一手机号或同一userid或同一ip, 一天只能发送N条
        if (!$this->_checkOtpExceed($otpType, $destination, $userId, $this->getIpAddress())) {
            $this->setResponseCode(MessageCode::CODE_OTP_SEND_EXCEED_RETRY);
            return false;
        }
        
        //获取未使用并未过期的otp
        $activeOtp = $this->_getActiveOtp($otpType, $destination, $userId);
        
        //如果存在可用的otp, 让其过期，并拿到code,创建新的记录
        $otpCode = NULL;
        if(!empty($activeOtp)){
            $otpCode = $activeOtp->getCode();
            $activeOtp->setExpiredAt(BaseDateTime::now());
            if (!$this->getRepository()->updateExpiredAt($activeOtp)) {
                $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
                return false;
            }
        }

        //创建新的otp纪录
        if(!empty($otpCode)){
            $entityOtp->setCode($otpCode);
        }

        $entityOtp->setCreatedBy($this->getUpdatedBy());
        $entityOtp->setCreatedAt(BaseDateTime::now());
        if (!$this->getRepository()->insert($entityOtp)) {
            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }

        //发送短信
        if(!$this->_sendOtp($entityOtp)){
            log_message("error", " generateOtp fail, sendOtp fail. " . json_encode($entityOtp));
            $this->setResponseCode(MessageCode::CODE_OTP_SEND_FAIL);
            return false;
        }
        
        $this->setResponseCode(MessageCode::CODE_OTP_HAS_BEEN_SENT);
        return $entityOtp;
    }

    public function checkFrequentAction($userId, $destination, $ipAddress){

        $limit = 30; //接口调用间隔限制为30秒，少于30秒则视为频繁操作

        if(!$otpEntity = $this->getRepository()->checkFrequentAction($userId, $destination, $ipAddress)){
            return true;
        }

        $now       = BaseDateTime::now()->getUnix();
        $updatedAt = $otpEntity->getUpdatedAt()->isNull() ? NULL: $otpEntity->getUpdatedAt()->getUnix();
        $createdAt = $otpEntity->getCreatedAt()->isNull() ? NULL: $otpEntity->getCreatedAt()->getUnix();

        if(!empty($updatedAt)){
            if($now - $updatedAt < $limit){
                return false;
            }
        }


        if(!empty($createdAt)){
            if($now - $createdAt < $limit){
                return false;
            }
        }

        return true;
    }

    public function verifyOtp($otpType, $destination, $user_id, $otpCode) {

        $activeOtp = $this->_getActiveOtp($otpType, $destination, $user_id);
        if(!$activeOtp){
            
            $tmpLogEntity = json_encode(array("user_id" => $user_id, "otp_type" => $otpType, "destination" => $destination, "otp_code" => $otpCode));
            log_message("error", "verifyOtp fail -> _getActiveOtp fail. $tmpLogEntity");
            
            $this->setResponseCode(MessageCode::CODE_OTP_VERIFY_FAIL);
            return false;
        }
        
        $oriEntity = clone($activeOtp);
        $activeOtp->setUpdatedBy($this->getUpdatedBy());
        if ($activeOtp->verify($otpCode)) {
            if ($this->getRepository()->updateVerifiedAt($activeOtp)) {
                
                $this->setResponseCode(MessageCode::CODE_OTP_VERIFY_SUCCESS);
                return $activeOtp;
            }
        }

        $this->setResponseCode(MessageCode::CODE_OTP_VERIFY_FAIL);
        return false;
    }

    protected function _buildOtpCode($otpType, $destination, $entityUserProfile = null) {
        
        $expired_at = $this->_getExpiredDateByType($otpType);
        
        $otp = new Otp();
        $otp->setIpAddress($this->getIpAddress());
        if($entityUserProfile != null){
            $otp->setUserId($entityUserProfile->getId());
        }
        $otp->generate();
        $otp->setOtpType($otpType);
        $otp->setExpiredAt($expired_at);
        $otp->setDestination($destination);

        return $otp;
    }

    protected function _getExpiredDateByType($otpType) {

        //default expired time(10 minutes)
        $default = 10;
        $coreconfig = CoreConfigDataService::build();

        if ($otpType == OtpType::EMAIL) {
            $period_in_min = $coreconfig->getConfig(CoreConfigType::OTP_EMAIL_PERIOD);
        } 
        else if ($otpType == OtpType::SMS) {
            $period_in_min = $coreconfig->getConfig(CoreConfigType::OTP_SMS_PERIOD);
        }
        if (!$period_in_min)
            $period_in_min = $default;

        $dt = BaseDateTime::now();
        $dt->addMinute($period_in_min);

        return $dt;
    }

    protected function _checkOtpExceed($otpType, $destination, $user_id = null, $ip_address = null) {
        
        $serviceCoreConfigData = CoreConfigDataService::build();
        $otp_limit_hour        = $serviceCoreConfigData->getConfig(CoreConfigType::OTP_SMS_LIMIT_PERIOD);
        $otp_sms_no            = $serviceCoreConfigData->getConfig(CoreConfigType::OTP_SMS_LIMIT);
        

        //24小时内，只能发送5条    
        if(!$otp_limit_hour){
            $otp_limit_hour = 24;
        }
        if(!$otp_sms_no){
            $otp_sms_no = 5;
        }
        
        $limit_time = BaseDateTime::now()->subHour($otp_limit_hour);
        
        $filter = new Otp();
        $filter->setOtpType($otpType);
        $filter->setCreatedFrom($limit_time);
        $filter->setDestination($destination);
        if($user_id != NULL){
            $filter->setUserId($user_id);
        }
        if($ip_address != NULL){
            $filter->setIpAddress($ip_address);
        }
        
        if ($count = $this->getRepository()->countOtp($filter)) {
            if ($count->count >= $otp_sms_no) {
                return false;
            }
        }
        
        return true;
    }

    protected function _getActiveOtp($otp_type, $destination, $user_id = null) {
        return $this->getRepository()->findActiveOtp($otp_type, $destination, $user_id);
    }
    
    //add message to queue
    protected function _sendOtp($entityOtp, $name = null){

        $otpCode  = $entityOtp->getCode();
        $mobileNo = $entityOtp->getDestination();
        $servSms = SmsService::build();
        if(!$servSms->sendSms($mobileNo, $otpCode, SmsService::SMS_TYPE_OTP)){
            return false;
        }

        return true;
    }

}
