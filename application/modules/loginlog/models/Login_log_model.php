<?php

use Common\Core\BaseDateTime;
use AccountService\LoginLog\LoginLog;
use AccountService\LoginLog\LoginLogCollection;

class Login_log_model extends Base_Model {

    private $tableName = 'login_log';
    private $selectFields = 'login_log.id
                ,login_log.ip_address
                ,login_log.address
                ,login_log.lat
                ,login_log.long
                ,login_log.user_id
                ,login_log.login_account_id
                ,login_log.status
                ,login_log.login_type
                ,login_log.user_agent
                ,login_log.attempt
                ,login_log.created_at
                ,login_log.created_by
                ,login_log.updated_at
                ,login_log.updated_by
                ,login_log.deleted_at
                ,login_log.deleted_by';

    public function map(stdClass $data) {

        $entity = new LoginLog();

        if (isset($data->id))
            $entity->setId($data->id);

        if (isset($data->address))
            $entity->setAddress($data->address);
        if (isset($data->lat))
            $entity->setLat($data->lat);
        if (isset($data->long))
            $entity->setLong($data->long);
        if (isset($data->ip_address))
            $entity->getIpAddress()->setIpAddressInteger($data->ip_address);
        if (isset($data->user_id))
            $entity->setUserId($data->user_id);
        if (isset($data->login_account_id))
            $entity->setLoginAccountId($data->login_account_id);
        if (isset($data->status))
            $entity->setStatus($data->status);
        if (isset($data->login_type))
            $entity->setLoginType($data->login_type);
        if (isset($data->user_agent))
            $entity->setUserAgent($data->user_agent);
        if (isset($data->attempt))
            $entity->setAttempt($data->attempt);
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

    public function insert(LoginLog $entity) {

        $this->db->set('address', $entity->getAddress());
        $this->db->set('lat', $entity->getLat());
        $this->db->set('long', $entity->getLong());
        $this->db->set('ip_address', $entity->getIpAddress()->getInteger());
        $this->db->set('user_id', $entity->getUserId());
        $this->db->set('login_account_id', $entity->getLoginAccountId());
        $this->db->set('status', $entity->getStatus());
        $this->db->set('login_type', $entity->getLoginType());
        $this->db->set('user_agent', $entity->getUserAgent());
        $this->db->set('attempt', $entity->getAttempt());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(LoginLog $entity, $isLogic = true) {
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

    public function update(LoginLog $entity) {
        if ($entity->getIpAddress())
            $this->db->set('ip_address', $entity->getIpAddress()->getInteger());
        if ($entity->getUserId())
            $this->db->set('user_id', $entity->getUserId());
        if ($entity->getLoginAccountId())
            $this->db->set('login_account_id', $entity->getLoginAccountId());
        if ($entity->getStatus())
            $this->db->set('status', $entity->getStatus());
        if ($entity->getLoginType())
            $this->db->set('login_type', $entity->getLoginType());
        if ($entity->getUserAgent())
            $this->db->set('user_agent', $entity->getUserAgent());
        if ($entity->getAttempt())
            $this->db->set('attempt', $entity->getAttempt());

        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(LoginLog $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

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
        if ($entity->getIpAddress()->getInteger()) {
            $this->db->where('ip_address', $entity->getIpAddress()->getInteger());
        }
        if ($entity->getUserId()) {
            $this->db->where('user_id', $entity->getUserId());
        }
        if ($entity->getLoginAccountId()) {
            $this->db->where('login_account_id', $entity->getLoginAccountId());
        }
        if ($entity->getStatus()) {
            $this->db->where('status', $entity->getStatus());
        }
        if ($entity->getLoginType()) {
            $this->db->where('login_type', $entity->getLoginType());
        }
        if ($entity->getUserAgent()) {
            $this->db->where('user_agent', $entity->getUserAgent());
        }
        if ($entity->getAttempt()) {
            $this->db->where('attempt', $entity->getAttempt());
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
            return $this->mapCollection($query->result(), new LoginLogCollection(), $total);
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
