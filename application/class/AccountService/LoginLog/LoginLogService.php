<?php

namespace AccountService\LoginLog;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;

class LoginLogService extends BaseService {

    protected static $_instance = NULL;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('loginlog/Login_log_model');
            self::$_instance = new LoginLogService($_ci->Login_log_model);
        }
        return self::$_instance;
    }

    public function addLoginLog(LoginLog $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_ADD_FAIL);
        return false;
    }

    public function deleteLoginLog($id, $isLogic = true) {

        $filter = new LoginLog();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_DELETE_FAIL);
        return false;
    }

    public function updateLoginLog(LoginLog $entity) {

        $filter = new LoginLog();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_UPDATE_FAIL);
        return false;
    }

    public function selectLoginLog(LoginLog $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_NOT_FOUND);
        return false;
    }

    public function getLoginLog($id) {

        if($entity = $this->getRepository()->getById($id)){
            $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_LOGIN_LOG_NOT_FOUND);
        return false;
    }

}
