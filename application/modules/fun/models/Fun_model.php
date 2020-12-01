<?php

use Common\Core\BaseDateTime;
use AccountService\Fun\Fun;
use AccountService\Fun\FunCollection;

class Fun_model extends Base_Model {

    private $tableName = 'fun';
    private $selectFields = 'fun.id
                ,fun.code
                ,fun.name
                ,fun.display_type
                ,fun.display_order
                ,fun.access_type
                ,fun.description
                ,fun.url
                ,fun.parent_id
                ,fun.created_at
                ,fun.created_by
                ,fun.updated_at
                ,fun.updated_by
                ,fun.deleted_at
                ,fun.deleted_by';

    public function map(stdClass $data) {

        $entity = new Fun();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->code))
            $entity->setCode($data->code);
        if (isset($data->name))
            $entity->setName($data->name);
        if (isset($data->display_type))
            $entity->setDisplayType($data->display_type);
        if (isset($data->display_order))
            $entity->setDisplayOrder($data->display_order);
        if (isset($data->access_type))
            $entity->setAccessType($data->access_type);
        if (isset($data->description))
            $entity->setDescription($data->description);
        if (isset($data->url))
            $entity->setUrl($data->url);
        if (isset($data->parent_id))
            $entity->setParentId($data->parent_id);
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

    public function insert(Fun $entity) {
        $this->db->set('code', $entity->getCode());
        $this->db->set('name', $entity->getName());
        $this->db->set('display_type', $entity->getDisplayType());
        $this->db->set('display_order', $entity->getDisplayOrder());
        $this->db->set('access_type', $entity->getAccessType());
        $this->db->set('description', $entity->getDescription());
        $this->db->set('url', $entity->getUrl());
        $this->db->set('parent_id', $entity->getParentId());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(Fun $entity, $isLogic = true) {
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

    public function update(Fun $entity) {
        if ($entity->getCode())
            $this->db->set('code', $entity->getCode());
        if ($entity->getName())
            $this->db->set('name', $entity->getName());
        if ($entity->getDisplayType())
            $this->db->set('display_type', $entity->getDisplayType());
        if ($entity->getDisplayOrder())
            $this->db->set('display_order', $entity->getDisplayOrder());
        if ($entity->getAccessType())
            $this->db->set('access_type', $entity->getAccessType());
        if ($entity->getDescription())
            $this->db->set('description', $entity->getDescription());
        if ($entity->getUrl())
            $this->db->set('url', $entity->getUrl());
        if ($entity->getParentId())
            $this->db->set('parent_id', $entity->getParentId());

        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(Fun $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

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
        if ($entity->getCode()) {
            $this->db->where('code', $entity->getCode());
        }
        if ($entity->getName()) {
            $this->db->where('name', $entity->getName());
        }
        if ($entity->getDisplayType()) {
            $this->db->where('display_type', $entity->getDisplayType());
        }
        if ($entity->getDisplayOrder()) {
            $this->db->where('display_order', $entity->getDisplayOrder());
        }
        if ($entity->getAccessType()) {
            $this->db->where('access_type', $entity->getAccessType());
        }
        if ($entity->getDescription()) {
            $this->db->where('description', $entity->getDescription());
        }
        if ($entity->getUrl()) {
            $this->db->where('url', $entity->getUrl());
        }
        if ($entity->getParentId()) {
            $this->db->where('parent_id', $entity->getParentId());
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
            return $this->mapCollection($query->result(), new FunCollection(), $total);
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

}
