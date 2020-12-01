<?php

namespace AccountService\IncrementTable;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;

class IncrementTableService extends BaseService {

    protected static $_instance = NULL;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('incrementtable/Increment_table_model');
            self::$_instance = new IncrementTableService($_ci->Increment_table_model);
        }
        return self::$_instance;
    }

    public function addIncrementTable(IncrementTable $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_ADD_FAIL);
        return false;
    }

    public function deleteIncrementTable($id, $isLogic = true) {

        $filter = new IncrementTable();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_DELETE_FAIL);
        return false;
    }

    public function updateIncrementTable(IncrementTable $entity) {

        $filter = new IncrementTable();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_UPDATE_FAIL);
        return false;
    }

    public function selectIncrementTable(IncrementTable $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_NOT_FOUND);
        return false;
    }

    public function getIncrementTable($id) {

        if($entity = $this->getRepository()->getById($id)){
            $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_INCREMENT_TABLE_NOT_FOUND);
        return false;
    }

}
