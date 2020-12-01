<?php

namespace AccountService\CoreConfigData;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use AccountService\Common\CacheKey;

class CoreConfigDataCommonService extends BaseService {

    protected static $_instance = NULL;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('coreconfigdata/Core_config_data_common_model');
            self::$_instance = new CoreConfigDataService($_ci->Core_config_data_common_model);
        }
        return self::$_instance;
    }

    public function checkExists($code, $withOutId = null){
        
        if($entity = $this->getRepository()->exists($code, $withOutId)){
            $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_ARELADY_EXISTS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_NOT_FOUND);
        return false;
    }
    
    public function addCoreConfigData(CoreConfigData $entity) {

        if($this->checkExists($entity->getCode())){
            return false;
        }
        
        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        //$this->_removeCache($entity);
        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_ADD_FAIL);
        return false;
    }

    public function deleteCoreConfigData($id, $isLogic = true) {

        $filter = new CoreConfigData();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        //$this->_removeCache($oldEntity);
        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_DELETE_FAIL);
        return false;
    }

    public function updateCoreConfigData(CoreConfigData $entity) {

        if($this->checkExists($entity->getCode(), $entity->getId())){
            return false;
        }
        
        $filter = new CoreConfigData();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        //$this->_removeCache($entity);
        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_UPDATE_FAIL);
        return false;
    }

    public function selectCoreConfigData(CoreConfigData $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_NOT_FOUND);
        return false;
    }

    public function getCoreConfigData($id) {
//        $cacheKey = CacheKey::CORE_CONFIG_DATA_ID . $id;
//        if(!$result = $this->getElasticCache($cacheKey)){
            if(!$entity = $this->getRepository()->getById($id)){
                $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_NOT_FOUND);
                return false;
            }
            
            $result = $entity;
//            $this->setElasticCache($cacheKey, $result);
//        }
        $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_GET_SUCCESS);
        return $result;
    }

    public function getCoreConfigDataByCodes($codes){
        
        if($result = $this->getRepository()->getByCodes($codes)){
            $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_GET_SUCCESS);
            return $result;
        }
        
        $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_NOT_FOUND);
        return false;
    }    
    
    public function getConfig($code){
        
//        $cacheKey = CacheKey::CORE_CONFIG_DATA_CODE . $code;
//        if(!$result = $this->getElasticCache($cacheKey)){
            if (!$entity = $this->getRepository()->getByCode($code)) {
                $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_NOT_FOUND);
                return false;
            }
            $result = $entity->getValue();
//            $this->setElasticCache($cacheKey, $result);
//        }
        return $result;
    }

    public function getCoreConfigDataByCode($code){
        
//        $cacheKey = CacheKey::CORE_CONFIG_DATA_CODE_ENTITY . $code;
//        if(!$entity = $this->getElasticCache($cacheKey)){
            if (!$entity = $this->getRepository()->getByCode($code)) {
                $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_NOT_FOUND);
                return false;
            }
//            $this->setElasticCache($cacheKey, $entity);
//        }
        $this->setResponseCode(MessageCode::CODE_CORE_CONFIG_DATA_GET_SUCCESS);
        return $entity;
    }
    
    protected function _removeCache(CoreConfigData $entity)
    {
        $cacheKeys = array( 
            CacheKey::CORE_CONFIG_DATA_ID, $entity->getId(),
            CacheKey::CORE_CONFIG_DATA_CODE . $entity->getCode()
        );
        
        foreach( $cacheKeys AS $key)
        {
            $this->deleteElastiCache($key);
        }
    }
    
}
