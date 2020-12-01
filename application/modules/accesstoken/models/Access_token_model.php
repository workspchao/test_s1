<?php

use Common\Core\BaseDateTime;
use AccountService\AccessToken\AccessToken;
use AccountService\AccessToken\AccessTokenCollection;

class Access_token_model extends Base_Model {

    private $tableName = 'access_token';
    private $selectFields = 'access_token.id
                ,access_token.user_id
                ,access_token.login_account_id
                ,access_token.token
                ,access_token.expired_at
                ,access_token.created_at
                ,access_token.created_by
                ,access_token.updated_at
                ,access_token.updated_by
                ,access_token.deleted_at
                ,access_token.deleted_by';

    public function map(stdClass $data) {

        $entity = new AccessToken();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->user_id))
            $entity->setUserId($data->user_id);
        if (isset($data->login_account_id))
            $entity->setLoginAccountId($data->login_account_id);
        if (isset($data->token))
            $entity->setToken($data->token);
        if (isset($data->expired_at))
            $entity->setExpiredAt(BaseDateTime::fromUnix($data->expired_at));
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

    public function insert(AccessToken $entity) {
        $this->db->set('user_id', $entity->getUserId());
        $this->db->set('login_account_id', $entity->getLoginAccountId());
        $this->db->set('token', $entity->getToken());
        $this->db->set('expired_at', $entity->getExpiredAt()->getUnix());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(AccessToken $entity, $isLogic = true) {
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

    public function update(AccessToken $entity) {
        if ($entity->getUserId())
            $this->db->set('user_id', $entity->getUserId());
        if ($entity->getLoginAccountId())
            $this->db->set('login_account_id', $entity->getLoginAccountId());
        if ($entity->getToken())
            $this->db->set('token', $entity->getToken());
        if ($entity->getExpiredAt())
            $this->db->set('expired_at', $entity->getExpiredAt()->getUnix());
        
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(AccessToken $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

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
        if ($entity->getUserId()) {
            $this->db->where('user_id', $entity->getUserId());
        }
        if ($entity->getLoginAccountId()) {
            $this->db->where('login_account_id', $entity->getLoginAccountId());
        }
        if ($entity->getToken()) {
            $this->db->where('token', $entity->getToken());
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
            return $this->mapCollection($query->result(), new AccessTokenCollection(), $total);
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

    public function findByLoginAccountID($login_account_id){
        
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);

        if(is_array($login_account_id)){
            $this->db->where_in('login_account_id', $login_account_id);
        }else{
            $this->db->where('login_account_id', $login_account_id);    
        }
        
        $this->db->where('deleted_at', NULL);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }
        return false;
    }

    public function findByLoginAccountIDs($login_account_id){
        
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        $this->db->where_in('login_account_id', $login_account_id);        
        $this->db->where('deleted_at', NULL);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $this->mapCollection($query->result(), new AccessTokenCollection(), 0);
        }
        return false;
    }

    public function findByToken($token) {
        
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        $this->db->where('token', $token);
        $this->db->where('deleted_at', NULL);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }

        return false;
    }

}
