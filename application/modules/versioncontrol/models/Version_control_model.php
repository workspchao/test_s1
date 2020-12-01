<?php

use Common\Core\BaseDateTime;
use AccountService\VersionControl\VersionControl;
use AccountService\VersionControl\VersionControlCollection;

class Version_control_model extends Base_Model {

    private $tableName = 'version_control';
    private $selectFields = 'version_control.id
                ,version_control.app_id
                ,version_control.app_key
                ,version_control.appname
                ,version_control.version
                ,version_control.platform
                ,version_control.download_url
                ,version_control.system_user_id
                ,version_control.hot_version
                ,version_control.hot_download_url
                ,version_control.created_at
                ,version_control.created_by
                ,version_control.updated_at
                ,version_control.updated_by
                ,version_control.deleted_at
                ,version_control.deleted_by';

    public function map(stdClass $data) {

        $entity = new VersionControl();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->app_id))
            $entity->setAppId($data->app_id);
        if (isset($data->app_key))
            $entity->setAppKey($data->app_key);
        if (isset($data->appname))
            $entity->setAppname($data->appname);
        if (isset($data->version))
            $entity->setVersion($data->version);
        if (isset($data->platform))
            $entity->setPlatform($data->platform);
        if (isset($data->download_url))
            $entity->setDownloadUrl($data->download_url);
        if (isset($data->system_user_id))
            $entity->setSystemUserId($data->system_user_id);
        
        if (isset($data->hot_version))
            $entity->setHotVersion($data->hot_version);
        if (isset($data->hot_download_url))
            $entity->setHotDownloadUrl($data->hot_download_url);
        
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

    public function insert(VersionControl $entity) {
        $this->db->set('app_id', $entity->getAppId());
        $this->db->set('app_key', $entity->getAppKey());
        $this->db->set('appname', $entity->getAppname());
        $this->db->set('version', $entity->getVersion());
        $this->db->set('platform', $entity->getPlatform());
        $this->db->set('download_url', $entity->getDownloadUrl());
        $this->db->set('system_user_id', $entity->getSystemUserId());
        $this->db->set('hot_version', $entity->getHotVersion());
        $this->db->set('hot_download_url', $entity->getHotDownloadUrl());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(VersionControl $entity, $isLogic = true) {
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

    public function update(VersionControl $entity) {
        if ($entity->getAppId())
            $this->db->set('app_id', $entity->getAppId());
        if ($entity->getAppKey())
            $this->db->set('app_key', $entity->getAppKey());
        if ($entity->getAppname())
            $this->db->set('appname', $entity->getAppname());
        if ($entity->getVersion())
            $this->db->set('version', $entity->getVersion());
        if ($entity->getPlatform())
            $this->db->set('platform', $entity->getPlatform());
        if ($entity->getDownloadUrl())
            $this->db->set('download_url', $entity->getDownloadUrl());
        if ($entity->getSystemUserId())
            $this->db->set('system_user_id', $entity->getSystemUserId());

        if ($entity->getHotVersion())
            $this->db->set('hot_version', $entity->getHotVersion());
        if ($entity->getHotDownloadUrl())
            $this->db->set('hot_download_url', $entity->getHotDownloadUrl());
        
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(VersionControl $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

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
        if ($entity->getAppId()) {
            $this->db->where('app_id', $entity->getAppId());
        }
        if ($entity->getAppKey()) {
            $this->db->where('app_key', $entity->getAppKey());
        }
        if ($entity->getAppname()) {
            $this->db->where('appname', $entity->getAppname());
        }
        if ($entity->getVersion()) {
            $this->db->where('version', $entity->getVersion());
        }
        if ($entity->getPlatform()) {
            $this->db->where('platform', $entity->getPlatform());
        }
        if ($entity->getDownloadUrl()) {
            $this->db->where('download_url', $entity->getDownloadUrl());
        }
        if ($entity->getSystemUserId()) {
            $this->db->where('system_user_id', $entity->getSystemUserId());
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
            return $this->mapCollection($query->result(), new VersionControlCollection(), $total);
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
