<?php

use Common\Core\BaseDateTime;
use AccountService\LoginAccount\LoginAccount;
use AccountService\LoginAccount\LoginAccountCollection;

class Login_account_model extends Base_Model {

    private $tableName = 'login_account';
    private $selectFields = 'login_account.id
                ,login_account.user_id
                ,login_account.login_type
                ,login_account.username
                ,login_account.salt
                ,login_account.password
                ,login_account.app_id
                ,login_account.created_at
                ,login_account.created_by
                ,login_account.updated_at
                ,login_account.updated_by
                ,login_account.deleted_at
                ,login_account.deleted_by';

    public function map(stdClass $data) {

        $entity = new LoginAccount();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->user_id))
            $entity->setUserId($data->user_id);
        if (isset($data->login_type))
            $entity->setLoginType($data->login_type);
        if (isset($data->username))
            $entity->setUsername($data->username);
        if (isset($data->salt))
            $entity->getPassword()->setSalt($data->salt);

        if (isset($data->password))
            $entity->getPassword()->setPassword($data->password);

//        if (isset($data->expired_at))
//            $entity->getPassword()->setExpiredAt(BaseDateTime::fromUnix($data->expired_at));

        if (isset($data->app_id))
            $entity->setAppId($data->app_id);
        
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

    public function insert(LoginAccount $entity) {
        $this->db->set('user_id', $entity->getUserId());
        $this->db->set('login_type', $entity->getLoginType());
        $this->db->set('username', $entity->getUsername());
        
        if ($entity->getPassword()->getSalt()) {
            $this->db->set('salt', $entity->getPassword()->getSalt());
        }
        
        if ($entity->getPassword()->getPassword()) {
            $this->db->set('password', $entity->getPassword()->getPassword());
        }
        
        $this->db->set('app_id', $entity->getAppId());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(LoginAccount $entity, $isLogic = true) {
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

    public function update(LoginAccount $entity) {
        if ($entity->getUserId())
            $this->db->set('user_id', $entity->getUserId());
        if ($entity->getLoginType())
            $this->db->set('login_type', $entity->getLoginType());
        if ($entity->getUsername())
            $this->db->set('username', $entity->getUsername());
        
        if ($entity->getPassword()->getSalt()) {
            $this->db->set('salt', $entity->getPassword()->getSalt());
        }
        
        if ($entity->getPassword()->getPassword()) {
            $this->db->set('password', $entity->getPassword()->getPassword());
        }
        
        if ($entity->getAppId())
            $this->db->set('app_id', $entity->getAppId());

        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(LoginAccount $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

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
        $user_id = $entity->getUserId();
        if (!empty($user_id)) {
            if(is_array($user_id)){
                $this->db->where_in('user_id', $user_id);
            }
            else{
                $this->db->where('user_id', $user_id);
            }
        }
        $loginType = $entity->getLoginType();
        if (!empty($loginType)) {
            if(is_array($loginType)){
                $this->db->where_in('login_type', $loginType);
            }
            else{
                $this->db->where('login_type', $loginType);
            }
        }
        if ($entity->getUsername()) {
            $this->db->where('username', $entity->getUsername());
        }
        if ($entity->getPassword()->getSalt()) {
            $this->db->where('salt', $entity->getPassword()->getSalt());
        }
        if ($entity->getPassword()->getPassword()) {
            $this->db->where('password', $entity->getPassword()->getPassword());
        }
        if ($entity->getAppId()) {
            $this->db->where('app_id', $entity->getAppId());
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
            return $this->mapCollection($query->result(), new LoginAccountCollection(), $total);
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
