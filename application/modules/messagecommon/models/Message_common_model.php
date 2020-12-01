<?php

use Common\Core\BaseDateTime;
use AccountService\MessageCommon\MessageCommon;
use AccountService\MessageCommon\MessageCommonCollection;
use Common\Core\Language;

class Message_common_model extends Base_Model {

    private $tableName = 'message_common';
    private $selectFields = 'message_common.id
                ,message_common.country_language_code
                ,message_common.code
                ,message_common.message
                ,message_common.created_at
                ,message_common.created_by
                ,message_common.updated_at
                ,message_common.updated_by
                ,message_common.deleted_at
                ,message_common.deleted_by';

    public function map(stdClass $data) {

        $entity = new MessageCommon();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->country_language_code))
            $entity->setCountryLanguageCode($data->country_language_code);
        if (isset($data->code))
            $entity->setCode($data->code);
        if (isset($data->message))
            $entity->setMessage($data->message);
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

    public function insert(MessageCommon $entity) {
        $this->db->set('country_language_code', $entity->getCountryLanguageCode());
        $this->db->set('code', $entity->getCode());
        $this->db->set('message', $entity->getMessage());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(MessageCommon $entity, $isLogic = true) {
        if ($isLogic) {
            $this->db->set('deleted_at', BaseDateTime::now()->getUnix());
            $this->db->set('deleted_by', $entity->getDeletedBy());
            $this->db->where('id', $entity->getId());
            if ($this->db->update($this->tableName)) {
                return true;
            }
        }
        else {
            $this->db->where('id', $entity->getId());
            if ($this->db->delete($this->tableName)) {
                return true;
            }
        }
        return false;
    }

    public function update(MessageCommon $entity) {
        if ($entity->getCountryLanguageCode())
            $this->db->set('country_language_code', $entity->getCountryLanguageCode());
        if ($entity->getCode())
            $this->db->set('code', $entity->getCode());
        if ($entity->getMessage())
            $this->db->set('message', $entity->getMessage());

        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(MessageCommon $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

        $total = 0;
        $offset = NULL;
        if($limit != NULL && $page != NULL){
            $offset = ($page - 1) * $limit;
        }

        $this->db->start_cache(); //to cache active record query
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        $this->db->where("deleted_at", NUll);

        if ($entity->getId()) {
            $this->db->where('id', $entity->getId());
        }
        if ($entity->getCountryLanguageCode()) {
            $this->db->where('country_language_code', $entity->getCountryLanguageCode());
        }
        if ($entity->getCode()) {
            $this->db->where('code', $entity->getCode());
        }
        if ($entity->getMessage()) {
            $this->db->where('message', $entity->getMessage());
        }
        if ($entity->getCreatedBy()) {
            $this->db->where('created_by', $entity->getCreatedBy());
        }
        if ($entity->getCreatedFrom() && !$entity->getCreatedFrom()->isNull()) {
            $this->db->where('created_at >= ', $entity->getCreatedFrom()->getUnix());
        }
        if ($entity->getCreatedTo() && !$entity->getCreatedTo()->isNull()) {
            $this->db->where('created_at <= ', $entity->getCreatedTo()->getUnix());
        }

        $this->db->stop_cache();

        $total = $this->db->count_all_results(); //to get total num of result w/o limit

        if($orderBy){
            $this->db->order_by($orderBy, null, false);
        }
        else{
            $this->db->order_by("created_at", "desc");
        }

        if($limit != NULL && $page != NULL){
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $this->mapCollection($query->result(), new MessageCommonCollection(), $total);
        }
        return false;
    }

    public function getById($id, $deleted = false ) {

        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        if(!$deleted){
            $this->db->where('deleted_at', NULL);
        }
        $this->db->where('id', $id);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }
        return false;
    }

    public function findByCode($code, Language $lang) {
        
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        $this->db->where('deleted_at', NULL);
        $this->db->where('code', $code);
        $this->db->where('country_language_code', $lang->getCode());

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }

        return false;
    }

}
