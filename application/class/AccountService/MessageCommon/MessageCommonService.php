<?php

namespace AccountService\MessageCommon;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use Common\Core\Language;
use AccountService\Common\CacheKey;

class MessageCommonService extends BaseService {

    protected static $_instance = NULL;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('messagecommon/Message_common_model');
            self::$_instance = new MessageCommonService($_ci->Message_common_model);
        }
        return self::$_instance;
    }

    public function addMessageCommon(MessageCommon $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_ADD_FAIL);
        return false;
    }

    public function deleteMessageCommon($id, $isLogic = true) {

        $filter = new MessageCommon();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_DELETE_FAIL);
        return false;
    }

    public function updateMessageCommon(MessageCommon $entity) {

        $filter = new MessageCommon();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            // $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_UPDATE_FAIL);
        return false;
    }

    public function selectMessageCommon(MessageCommon $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_NOT_FOUND);
        return false;
    }

    public function getMessageCommon($id) {

        if($entity = $this->getRepository()->getById($id)){
            $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_MESSAGE_COMMON_NOT_FOUND);
        return false;
    }

    public function getMessage($code, $lang_code = 'zh-CN')
    {
        if($code == null){
            return null;
        }
        
        if ($lang_code == NULL)
            $lang = new Language();
        else
            $lang = new Language($lang_code);
        
        $cacheKey = CacheKey::MESSAGE_COMMON_CODE . "." . $code . $lang->getCode();
        
        if(!$result = $this->getElasticCache($cacheKey)){
            if (!$result = $this->getRepository()->findByCode($code, $lang)) {
                return false;
            }
            $this->setElasticCache($cacheKey, $result);
        }
        return $result->getMessage();
    }
    
    public function updateMessage($code, $message, $lang_code = 'zh-CN'){

        if($code == null){
            return null;
        }


        if ($lang_code == NULL)
            $lang = new Language();
        else
            $lang = new Language($lang_code);

        $cacheKey = CacheKey::MESSAGE_COMMON_CODE . "." . $code . $lang->getCode();


        if (!$entity = $this->getRepository()->findByCode($code, $lang)) {
            return false;
        }

        $entity->setMessage($message);
        $this->updateMessageCommon($entity);
        $this->setElasticCache($cacheKey, $entity);
        
        return true;
    }
}
