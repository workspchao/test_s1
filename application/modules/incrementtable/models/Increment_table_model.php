<?php

use AccountService\IncrementTable\IncrementTable;
use Common\Core\BaseDateTime;

class increment_table_model extends Base_Model{

    /*
     * override this function to have a separate db connection
     */
    public function TransStart()
    {
        $this->db->trans_start();
    }

    public function TransRollback()
    {
        $this->db->trans_rollback();
    }

    public function TransComplete()
    {
        $this->db->trans_complete();
    }

    
    private $tableName = 'increment_table';
    private $selectFields = 'increment_table.id
                ,increment_table.attribute
                ,increment_table.value
                ,increment_table.last_increment_date
                ,increment_table.prefix
                ,increment_table.suffix
                ,increment_table.created_at
                ,increment_table.created_by
                ,increment_table.updated_at
                ,increment_table.updated_by
                ,increment_table.deleted_at
                ,increment_table.deleted_by';

    public function map(stdClass $data) {

        $entity = new IncrementTable();

        if (isset($data->id))
            $entity->setId($data->id);
        if (isset($data->attribute))
            $entity->setAttribute($data->attribute);
        if (isset($data->value))
            $entity->setValue($data->value);
        if (isset($data->last_increment_date))
            $entity->setLastIncrementDate(BaseDateTime::fromUnix($data->last_increment_date));
        if (isset($data->prefix))
            $entity->setPrefix($data->prefix);
        if (isset($data->suffix))
            $entity->setSuffix($data->suffix);
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

    public function insert(IncrementTable $entity) {
        $this->db->set('attribute', $entity->getAttribute());
        $this->db->set('value', $entity->getValue());
        $this->db->set('last_increment_date', $entity->getLastIncrementDate()->getUnix());
        $this->db->set('prefix', $entity->getPrefix());
        $this->db->set('suffix', $entity->getSuffix());
        $this->db->set('created_at', BaseDateTime::now()->getUnix());
        $this->db->set('created_by', $entity->getCreatedBy());
        if ($this->db->insert($this->tableName)) {
            $id = $this->db->insert_id();
            $entity->setId($id);
            return $entity;
        }
        return false;
    }

    public function delete(IncrementTable $entity, $isLogic = true) {
        if ($isLogic) {
            $this->db->set('deleted_at', BaseDateTime::now()->getUnix());
            $this->db->set('deleted_by', $entity->getDeletedBy());
            $this->db->where('id', $entity->getId());
            if ($this->db->update($this->tableName)) {
                if($this->db->affected_rows() > 0)
                {
                    return true;
                }
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

    public function update(IncrementTable $entity) {
        if ($entity->getAttribute())
            $this->db->set('attribute', $entity->getAttribute());
        if ($entity->getValue())
            $this->db->set('value', $entity->getValue());
        if ($entity->getLastIncrementDate())
            $this->db->set('last_increment_date', $entity->getLastIncrementDate()->getUnix());
        if ($entity->getPrefix())
            $this->db->set('prefix', $entity->getPrefix());
        if ($entity->getSuffix())
            $this->db->set('suffix', $entity->getSuffix());

        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $entity->getUpdatedBy());

        $this->db->where('id', $entity->getId());

        if ($this->db->update($this->tableName)) {
            return $entity;
        }
        return false;
    }

    public function select(IncrementTable $entity, $orderBy = NULL, $limit = NULL, $page = NULL ) {

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
        if ($entity->getAttribute()) {
            $this->db->where('attribute', $entity->getAttribute());
        }
        if ($entity->getValue()) {
            $this->db->where('value', $entity->getValue());
        }
        if ($entity->getLastIncrementDate()) {
            $this->db->where('last_increment_date', $entity->getLastIncrementDate());
        }
        if ($entity->getPrefix()) {
            $this->db->where('prefix', $entity->getPrefix());
        }
        if ($entity->getSuffix()) {
            $this->db->where('suffix', $entity->getSuffix());
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
            return $this->mapCollection($query->result(), new IncrementTableCollection(), $total);
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

    public function findByAttribute($attribute)
    {
        $query = $this->db->query("SELECT
                                       id,
                                       attribute,
                                       `value`,
                                       last_increment_date,
                                       prefix,
                                       suffix,
                                       created_at,
                                       created_by,
                                       updated_at,
                                       updated_by,
                                       deleted_at,
                                       deleted_by
                                       FROM increment_table where attribute = '"
                                        . $attribute . "'
                                       and deleted_at is null
                                       for update" );

        if($query->num_rows() >  0)
        {
            return $this->map($query->row());
        }

        return false;
    }

    public function updateIncrementNumber(IncrementTable $data)
    {
        $this->db->set('value', $data->getValue());
        $this->db->set('last_increment_date',BaseDateTime::now()->getUnix());
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $data->getUpdatedBy());
        $this->db->where('id', $data->getId());
        $this->db->where('deleted_at', NULL);

        if ($this->db->update($this->tableName))
        {
            return $this->db->affected_rows();
        }

        return false;
    }

    public function addIncrementNumber(IncrementTable $data, $value)
    {
        $this->db->set('value', "value + $value", FALSE);
        $this->db->set('last_increment_date', BaseDateTime::now()->getUnix());
        $this->db->set('updated_at', BaseDateTime::now()->getUnix());
        $this->db->set('updated_by', $data->getUpdatedBy());
        $this->db->where('id', $data->getId());
        $this->db->where('deleted_at', NULL);

        if ($this->db->update($this->tableName))
        {
            return $this->db->affected_rows();
        }

        return false;
    }

}