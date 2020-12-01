<?php

namespace Common\Core;

class EntitySelector implements \Iterator, \Countable
{

    protected $conditions = array();
    protected $limit;
    protected $page;
    protected $orderConditions = array();

    //types
    const EQUALS = 'equals';
    const EQUALS_IN = 'equals_in';
    const BETWEEN = 'between';
    const LESS_THAN = 'less_than';
    const GREATER_THAN = 'greater_than';
    const LESS_AND_EQUAL_THAN = 'less_and_equal_than';
    const GREATER_AND_EQUAL_THAN = 'greater_and_equal_than';
    const MULTIPLE = 'multiple';

    public function reset()
    {
        $this->conditions = array();
        $this->orderConditions = array();
    }

    public function limit($limit, $page = 1)
    {
        $this->limit = $limit;
        $this->page = $page;
        return $this;
    }

    public function getLimit()
    {
        if (!is_null($this->limit) and !is_null($this->page)) {
            $offset = ($this->page - 1) * $this->limit;
            return array($this->limit, $offset);
        }

        return null;
    }

    public function order($field, $ascending = true)
    {
        $this->orderConditions[] = new orderCondition($field, $ascending);
        return $this;
    }

    public function getOrderConditions()
    {
        return $this->orderConditions;
    }

    public function equals($field, $value)
    {
        $this->conditions[] = new entityCondition(self::EQUALS, $field, $value);
        return $this;
    }

    public function equalsIn($field, array $values)
    {
        $this->conditions[] = new entityCondition(self::EQUALS_IN, $field, $values);
        return $this;
    }

    public function between($field, $value1, $value2)
    {
        $this->conditions[] = new entityCondition(self::BETWEEN, $field, $value1, $value2);
        return $this;
    }

    public function greaterThan($field, $value)
    {
        $this->conditions[] = new entityCondition(self::GREATER_THAN, $field, $value);
        return $this;
    }

    public function greaterAndEqualThan($field, $value)
    {
        $this->conditions[] = new entityCondition(self::GREATER_AND_EQUAL_THAN, $field, $value);
        return $this;
    }

    public function lesserThan($field, $value)
    {
        $this->conditions[] = new entityCondition(self::LESS_THAN, $field, $value);
        return $this;
    }

    public function lesserAndEqualThan($field, $value)
    {
        $this->conditions[] = new entityCondition(self::LESS_AND_EQUAL_THAN, $field, $value);
        return $this;
    }

    public function multipleCondition(EntitySelector $selector, $and = true)
    {
        $this->conditions[] = new entityCondition(self::MULTIPLE, NULL, $selector, NULL, $and);
        return $this;
    }

    //public function orderBy(){}
    //public function limit(){}

    // ITERATOR INTERFACE
    private $position = 0;

    function rewind()
    {
        $this->position = 0;
    }

    /**
     * 
     * @return entityCondition
     */
    function current()
    {
        return $this->conditions[$this->position];
    }

    function key()
    {
        return $this->position;
    }

    function next()
    {
        ++$this->position;
    }

    function valid()
    {
        return isset($this->conditions[$this->position]);
    }

    public function count()
    {
        return count($this->conditions);
    }
}

class entityCondition
{

    public $conditionType;
    public $isAnd = true;   //otherwise or
    public $field;
    public $value1;
    public $value2;

    public function __construct($conditionType, $field, $value1, $value2 = null, $isAnd = true)
    {
        $this->conditionType = $conditionType;
        $this->field = $field;
        $this->value1 = $value1;
        $this->value2 = $value2;
        if ($isAnd)
            $this->isAnd = true;
        else
            $this->isAnd = false;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getValue1()
    {
        return $this->value1;
    }

    public function getValue2()
    {
        return $this->value2;
    }

    public function getConditionType()
    {
        return $this->conditionType;
    }

    public function getIsAnd()
    {
        return $this->isAnd;
    }
}

class orderCondition
{

    public $field;
    public $acs = true;

    public function __construct($field, $acs = true)
    {
        $this->field = $field;
        $this->acs = $acs;
    }

    public function getField()
    {
        return $this->field;
    }

    public function isAscending()
    {
        return $this->acs ? true : false;
    }
}
