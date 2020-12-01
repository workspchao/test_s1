<?php

use Common\Core\BaseDateTime;
use AccountService\Otp\Otp;
use AccountService\Otp\OtpCollection;

class Otp_model extends Base_Model {

    private $tableName = 'otp';
    private $selectFields = 'otp.id
                ,otp.ip_address
                ,otp.user_id
                ,otp.otp_type
                ,otp.code
                ,otp.destination
                ,otp.expired_at
                ,otp.verified_at
                ,otp.created_at
                ,otp.created_by
                ,otp.updated_at
                ,otp.updated_by
                ,otp.deleted_at
                ,otp.deleted_by';

    public function map(stdClass $data) {

        $entity = new Otp();

        if (isset($data->id))
            $entity->setId($data->id);
        if( isset($data->ip_address) )
            $entity->getIpAddress()->setIpAddressInteger($data->ip_address);
        if (isset($data->user_id))
            $entity->setUserId($data->user_id);
        if (isset($data->otp_type))
            $entity->setOtpType($data->otp_type);
        if (isset($data->code))
            $entity->setCode($data->code);
        if (isset($data->destination))
            $entity->setDestination($data->destination);
        if (isset($data->expired_at))
            $entity->setExpiredAt(BaseDateTime::fromUnix($data->expired_at));
        if (isset($data->verified_at))
            $entity->setVerifiedAt(BaseDateTime::fromUnix($data->verified_at));
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

    public function insert(Otp $entity) {
        
        $this->db->set('ip_address', $entity->getIpAddress()->getInteger());
        $this->db->set('user_id', $entity->getUserId());
        $this->db->set('otp_type', $entity->getOtpType());
        $this->db->set('code', $entity->getCode());
        $this->db->set('destination', $entity->getDestination());
        $this->db->set('expired_at', $entity->getExpiredAt()->getUnix());
        if($entity->getVerifiedAt() && !$entity->getVerifiedAt()->isNull())
            $this->db->set('verified_at', $entity->getVerifiedAt()->getUnix());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(Otp $entity, $isLogic = true) {
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

    public function update(Otp $entity) {
        
        if ($entity->getIpAddress())
            $this->db->set('ip_address', $entity->getIpAddress()->getInteger());
        if ($entity->getUserId())
            $this->db->set('user_id', $entity->getUserId());
        if ($entity->getOtpType())
            $this->db->set('otp_type', $entity->getOtpType());
        if ($entity->getCode())
            $this->db->set('code', $entity->getCode());
        if ($entity->getDestination())
            $this->db->set('destination', $entity->getDestination());
        if ($entity->getExpiredAt() && !$entity->getExpiredAt()->isNull())
            $this->db->set('expired_at', $entity->getExpiredAt());
        if($entity->getVerifiedAt() && !$entity->getVerifiedAt()->isNull())
            $this->db->set('verified_at', $entity->getVerifiedAt()->getUnix());

        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(Otp $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

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
        if ($entity->getIpAddress() && $entity->getIpAddress()->getInteger()) {
            $this->db->where('ip_address', $entity->getIpAddress()->getInteger());
        }
        if ($entity->getUserId()) {
            $this->db->where('user_id', $entity->getUserId());
        }
        if ($entity->getOtpType()) {
            $this->db->where('otp_type', $entity->getOtpType());
        }
        if ($entity->getCode()) {
            $this->db->where('code', $entity->getCode());
        }
        if ($entity->getDestination()) {
            $this->db->where('destination', $entity->getDestination());
        }
//        if ($entity->getExpiredAt()) {
//            $this->db->where('expired_at', $entity->getExpiredAt());
//        }
//        if ($entity->getVerifiedAt()) {
//            $this->db->where('verified_at', $entity->getVerifiedAt());
//        }
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
            return $this->mapCollection($query->result(), new OtpCollection(), $total);
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
    
    public function findActiveOtp($otp_type, $destination, $user_id = null){
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);
        $this->db->where('deleted_at', NULL);
        $this->db->where('otp_type', $otp_type);
        $this->db->where('destination', $destination);
        $this->db->where("expired_at >", BaseDateTime::now()->getUnix());
        $this->db->where("verified_at", NULL);
        if($user_id != null){
            $this->db->where('user_id', $user_id);
        }

        $this->db->order_by("id", "desc");
        
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }

        return false;
    }
    
    public function updateExpiredAt(Otp $otp)
    {
        $this->db->set('expired_at', $otp->getExpiredAt()->getUnix());
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $otp->getUpdatedBy());

        $this->db->where('id', $otp->getId());
        $this->db->where('expired_at !=', $otp->getExpiredAt()->getUnix());

        if( $this->db->update('otp') )
        {
            return true;
        }

        return false;
    }
    
    public function updateVerifiedAt(Otp $otp)
    {
        $this->db->set('verified_at', $otp->getVerifiedAt()->getUnix());
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $otp->getUpdatedBy());

        $this->db->where('id', $otp->getId());
        $this->db->where('verified_at', NULL);

        if( $this->db->update('otp') )
        {
            return true;
        }

        return false;
    }
    

    public function checkFrequentAction($userId, $destination, $ipAddress){
        $this->db->select($this->selectFields);
        $this->db->from($this->tableName);

        if(!empty($userId)){
            $this->db->or_where("user_id", $userId);
        }
        
        if(!empty($destination)){
            $this->db->or_where("destination", $destination);
        }

        if(!empty($ipAddress)){
            if(!empty($ipAddress->getInteger())){
                $this->db->or_where("ip_address", $ipAddress->getInteger());
            }
        }

        $this->db->order_by("id", "desc");

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $this->map($query->row());
        }

        return false;
    }

    public function countOtp(Otp $entity){
        $this->db->select("count(*) as count");
        $this->db->from($this->tableName);


        $this->db->group_start();
        if(!empty($entity->getUserId())){
            $this->db->or_where("user_id", $entity->getUserId());
        }

        if(!empty($entity->getDestination())){
            $this->db->or_where("destination", $entity->getDestination());
        }

        if (!empty($entity->getIpAddress()) && !empty($entity->getIpAddress()->getInteger())) {
            $this->db->or_where('ip_address', $entity->getIpAddress()->getInteger());
        }

        $this->db->group_end();

        if (!empty($entity->getOtpType())) {
            $this->db->where('otp_type', $entity->getOtpType());
        }

        if ($entity->getCreatedFrom() && !$entity->getCreatedFrom()->isNull()) {
            $this->db->where('created_at >= ', $entity->getCreatedFrom()->getUnix());
        }

        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return false;
    }
}
