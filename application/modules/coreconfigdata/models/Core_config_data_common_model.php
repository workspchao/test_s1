<?php

use Common\Core\BaseDateTime;
use AccountService\CoreConfigData\CoreConfigData;
use AccountService\CoreConfigData\CoreConfigDataCollection;

class Core_config_data_common_model extends Common_Base_Model {

    private $tableName = 'core_config_data';
    private $selectFields = 'core_config_data.id
                ,core_config_data.code
                ,core_config_data.value
                ,core_config_data.description
                ,core_config_data.created_at
                ,core_config_data.created_by
                ,core_config_data.updated_at
                ,core_config_data.updated_by
                ,core_config_data.deleted_at
                ,core_config_data.deleted_by';

    public function map(stdClass $data) {

        $entity = new CoreConfigData();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->code))
            $entity->setCode($data->code);
        if (isset($data->value))
            $entity->setValue($data->value);
        if (isset($data->description))
            $entity->setDescription($data->description);
        if (isset($data->created_at))
            $entity->setCreatedAt(BaseDateTime::fromUnix($data->created_at));
        if (isset($data->created_by))
            $entity->setCreatedBy($data->created_by);
        if (isset($data->updated_at))
            $entity->setUpdatedAt(BaseDateTime::fromUnix($data->updated_at));
        if (isset($data->updated_by))
            $entity->setUpdatedBy($data->updated_by);
        if (isset($data->deleted_at))
            $entity->setDeletedAt(BaseDateTime::fromUnix($data->deleted_at));
        if (isset($data->deleted_by))
            $entity->setDeletedBy($data->deleted_by);

        return $entity;
    }

    public function exists($code, $withOutId = null) {

        $this->common_db->select($this->selectFields);
        $this->common_db->from($this->tableName);
        $this->common_db->where('deleted_at', NULL);
        $this->common_db->where('code', $code);
        if($withOutId){
            $this->common_db->where('id <>', $withOutId);
        }

        $query = $this->common_db->get();

        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }
        return false;
    }

    public function insert(CoreConfigData $entity) {
        $this->common_db->set('code', $entity->getCode());
        $this->common_db->set('value', $entity->getValue());
        $this->common_db->set('description', $entity->getDescription());
        $this->common_db->set('created_at', BaseDateTime::now()->getUnix());
        $this->common_db->set('created_by', $entity->getCreatedBy());
        if ($this->common_db->insert($this->tableName)) {
            $id = $this->common_db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(CoreConfigData $entity, $isLogic = true) {
        if ($isLogic) {
            $this->common_db->set('deleted_at', BaseDateTime::now()->getUnix());
            $this->common_db->set('deleted_by', $entity->getDeletedBy());
            $this->common_db->where('id', $entity->getId());
            if ($this->common_db->update($this->tableName)) {
                return true;
            }
        }
        else {
            $this->common_db->where('id', $entity->getId());
            if ($this->common_db->delete($this->tableName)) {
                return true;
            }
        }
        return false;
    }

    public function update(CoreConfigData $entity) {
        if ($entity->getCode())
            $this->common_db->set('code', $entity->getCode());
        // if ($entity->getValue())
            $this->common_db->set('value', $entity->getValue());
        if ($entity->getDescription())
            $this->common_db->set('description', $entity->getDescription());

        $this->common_db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->common_db->set('updated_by', $entity->getUpdatedBy());

        $this->common_db->where('id', $entity->getId());

        if ($this->common_db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(CoreConfigData $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

        $total = 0;
        $offset = NULL;
        if($limit != NULL && $page != NULL){
            $offset = ($page - 1) * $limit;
        }

        $this->common_db->start_cache(); //to cache active record query
        $this->common_db->select($this->selectFields);
        $this->common_db->from($this->tableName);
        $this->common_db->where("deleted_at", NUll);

        if ($entity->getId()) {
            $this->common_db->where('id', $entity->getId());
        }
        if ($entity->getCode()) {
            $this->common_db->where('code', $entity->getCode());
        }
        if ($entity->getValue()) {
            $this->common_db->where('value', $entity->getValue());
        }
        if ($entity->getDescription()) {
            $this->common_db->where('description', $entity->getDescription());
        }
        if ($entity->getCreatedBy()) {
            $this->common_db->where('created_by', $entity->getCreatedBy());
        }
        if ($entity->getCreatedFrom() && !$entity->getCreatedFrom()->isNull()) {
            $this->common_db->where('created_at >= ', $entity->getCreatedFrom()->getUnix());
        }
        if ($entity->getCreatedTo() && !$entity->getCreatedTo()->isNull()) {
            $this->common_db->where('created_at <= ', $entity->getCreatedTo()->getUnix());
        }

        $this->common_db->stop_cache();

        $total = $this->common_db->count_all_results(); //to get total num of result w/o limit

        if($orderBy){
            $this->common_db->order_by($orderBy, null, false);
        }
        else{
            $this->common_db->order_by("created_at", "desc");
        }

        if($limit != NULL && $page != NULL){
            $this->common_db->limit($limit, $offset);
        }
        $query = $this->common_db->get();
        $this->common_db->flush_cache();

        if ($query->num_rows() > 0) {
            return $this->mapCollection($query->result(), new CoreConfigDataCollection(), $total);
        }
        return false;
    }

    public function getById($id, $deleted = false ) {

        $this->common_db->select($this->selectFields);
        $this->common_db->from($this->tableName);
        if(!$deleted){
            $this->common_db->where('deleted_at', NULL);
        }
        $this->common_db->where('id', $id);

        $query = $this->common_db->get();

        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }
        return false;
    }

    public function getByCode($code) {

        $this->common_db->select($this->selectFields);
        $this->common_db->from($this->tableName);
        $this->common_db->where('deleted_at', NULL);
        $this->common_db->where('code', $code);

        $query = $this->common_db->get();

        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }
        return false;
    }

    public function getByCodes($codes) {

        $this->common_db->select("id, code, value, description");
        $this->common_db->from($this->tableName);
        $this->common_db->where('deleted_at', NULL);
        $this->common_db->where_in('code', $codes);

        $query = $this->common_db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }
}
