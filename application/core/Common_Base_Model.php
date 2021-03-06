<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Common\Core\entityCondition;
use Common\Core\EntitySelector;
use Common\Core\BaseDateTime;
use Common\Core\PaginatedResult;
use Common\Core\SearchableFieldNameConverter;

class Common_Base_Model extends CI_Model
{
    protected $frCreatedAt;
    protected $toCreatedAt;

    function __construct() {
        parent::__construct();

        date_default_timezone_set('Asia/Shanghai');
        
        $this->common_db = $this->load->database('common', true);
        
        $this->frCreatedAt = new BaseDateTime();
        $this->toCreatedAt = new BaseDateTime();
    }

    public function TransStart()
    {
        $this->common_db->trans_start();
    }

    public function TransRollback()
    {
        $this->common_db->trans_rollback();
    }

    public function TransComplete()
    {
        $this->common_db->trans_complete();
    }

    public function mapCollection(array $data, $collection, $total)
    {
        foreach($data AS $info)
        {
            $entity = $this->map($info);
            $collection->addData($entity);
        }

        if( $collection->count() > 0 )
        {
            $object = new PaginatedResult();
            $object->result = $collection;
            $object->total = $total;
            return $object;
        }

        return false;
    }

    public function setFromCreatedAt(BaseDateTime $dt)
    {
        $this->frCreatedAt = $dt;
        return $this;
    }

    public function getFromCreatedAt()
    {
        return $this->frCreatedAt;
    }

    public function setToCreatedAt(BaseDateTime $dt)
    {
        $this->toCreatedAt = $dt;
        return $this;
    }

    public function getToCreatedAt()
    {
        return $this->toCreatedAt;
    }
    
    protected function _conditionStatement(EntitySelector $selector, SearchableFieldNameConverter $converter)
    {
        foreach($selector AS $condition)
            $this->_addWhereCondition($condition, $converter);
        
        if( list($page, $offset) = $selector->getLimit() )
            $this->common_db->limit($page, $offset);
        
        foreach($selector->getOrderConditions() AS $orderCondition)
            $this->common_db->order_by($converter->convertFieldName($orderCondition->getField()), $orderCondition->isAscending() ? 'asc':'desc');
    }
    
    protected function _addWhereConditionGroup(entityCondition $condition, SearchableFieldNameConverter $converter)
    {
        if( $condition->getValue1() instanceof EntitySelector AND
            count($condition->getValue1()) > 0 )
        {
            if( $condition->getIsAnd() )
                $this->common_db->group_start();
            else
                $this->common_db->or_group_start();
            
            foreach($condition->getValue1() AS $condition)
                $this->_addWhereCondition($condition, $converter);
            
            $this->common_db->group_end();
        }
    }
    
    protected function _addWhereCondition(entityCondition $condition, SearchableFieldNameConverter $converter)
    {
        if( $condition->getConditionType() != EntitySelector::MULTIPLE )    //special handling for multiple
            list($f, $v1, $v2) = $converter->convert($condition);
        
        switch( $condition->getConditionType() )
        {
            case EntitySelector::EQUALS:
                $this->common_db->where($f, $v1);
                break;
            case EntitySelector::EQUALS_IN:
                $this->common_db->where_in($f, $v1);    //todo $v should be array
                break;
            case EntitySelector::BETWEEN:
                $this->common_db->where("$f BETWEEN $v1 AND $v2");
                break;
            case EntitySelector::GREATER_THAN:
                $this->common_db->where("$f > $v1");
                break;
            case EntitySelector::LESS_THAN:
                $this->common_db->where("$f < $v1");
                break;
            case EntitySelector::GREATER_AND_EQUAL_THAN:
                $this->common_db->where("$f >= $v1");
                break;
            case EntitySelector::LESS_AND_EQUAL_THAN:
                $this->common_db->where("$f <= $v1");
                break;
            case EntitySelector::MULTIPLE:
                $this->_addWhereConditionGroup($condition, $converter);
                break;
        }
    }
}
