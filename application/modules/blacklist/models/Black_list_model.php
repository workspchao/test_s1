<?php

use Common\Core\BaseDateTime;
use AccountService\BlackList\BlackList;
use AccountService\BlackList\BlackListCollection;

class Black_list_model extends Base_Model {

    private $tableName = 'black_list';
    private $selectFields = 'black_list.id
                ,black_list.type
                ,black_list.level
                ,black_list.ip_address
                ,black_list.user_id
                ,black_list.status
                ,black_list.released_by
                ,black_list.released_at
                ,black_list.remarks
                ,black_list.created_at
                ,black_list.created_by
                ,black_list.updated_at
                ,black_list.updated_by
                ,black_list.deleted_at
                ,black_list.deleted_by';

    public function map(stdClass $data) {

        $entity = new BlackList();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->type))
            $entity->setType($data->type);
        if (isset($data->level))
            $entity->setLevel($data->level);
        if (isset($data->ip_address))
            $entity->getIpAddress()->setIpAddressInteger($data->ip_address);
        if (isset($data->user_id))
            $entity->setUserId($data->user_id);
        if (isset($data->status))
            $entity->setStatus($data->status);
        if (isset($data->released_by))
            $entity->setReleasedBy($data->released_by);
        if (isset($data->released_at))
            $entity->setReleasedAt(BaseDateTime::fromUnix($data->released_at));
        if (isset($data->remarks))
            $entity->setRemarks($data->remarks);
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

    public function insert(BlackList $entity) {
        $this->db->set('type', $entity->getType());
        $this->db->set('level', $entity->getLevel());
        $this->db->set('ip_address', $entity->getIpAddress()->getInteger());
        $this->db->set('user_id', $entity->getUserId());
        $this->db->set('status', $entity->getStatus());
        $this->db->set('released_by', $entity->getReleasedBy());
        $this->db->set('released_at', $entity->getReleasedAt()->getUnix());
        $this->db->set('remarks', $entity->getRemarks());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(BlackList $entity, $isLogic = true) {
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

    public function update(BlackList $entity) {
        if ($entity->getType())
            $this->db->set('type', $entity->getType());
        if ($entity->getLevel())
            $this->db->set('level', $entity->getLevel());
        if ($entity->getIpAddress())
            $this->db->set('ip_address', $entity->getIpAddress()->getInteger());
        if ($entity->getUserId())
            $this->db->set('user_id', $entity->getUserId());
        if ($entity->getStatus())
            $this->db->set('status', $entity->getStatus());
        if ($entity->getReleasedBy())
            $this->db->set('released_by', $entity->getReleasedBy());
        if ($entity->getReleasedAt())
            $this->db->set('released_at', $entity->getReleasedAt()->getUnix());
        if ($entity->getRemarks())
            $this->db->set('remarks', $entity->getRemarks());

        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(BlackList $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

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
        if ($entity->getType()) {
            $this->db->where('type', $entity->getType());
        }
        if ($entity->getLevel()) {
            $this->db->where('level', $entity->getLevel());
        }
        if ($entity->getIpAddress()->getInteger()) {
            $this->db->where('ip_address', $entity->getIpAddress()->getInteger());
        }
        if ($entity->getUserId()) {
            $this->db->where('user_id', $entity->getUserId());
        }
        if ($entity->getStatus()) {
            $this->db->where('status', $entity->getStatus());
        }
        if ($entity->getReleasedBy()) {
            $this->db->where('released_by', $entity->getReleasedBy());
        }
        if (!$entity->getReleasedAt()->isNull()) {
            $this->db->where('released_at', $entity->getReleasedAt()->getUnix());
        }
        if ($entity->getRemarks()) {
            $this->db->where('remarks', $entity->getRemarks());
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
            return $this->mapCollection($query->result(), new BlackListCollection(), $total);
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
