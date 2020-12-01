<?php

namespace Common\Core;

class SearchableFieldNameConverter
{

    protected $alias;

    function __construct($alias = null)
    {
        $this->alias = $alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getAliasWithDot()
    {
        if ($this->alias)
            return $this->alias . ".";
        else
            return null;
    }

    public function convertFieldName($field)
    {
        return $this->getAliasWithDot() . $field;
    }

    public function convertValue($field, $value)
    {
        return $value;
    }

    public function convert(entityCondition $condition)
    {
        $cField = $this->convertFieldName($condition->getField());
        if (is_array($condition->getValue1())) {
            $cValue1 = array();
            foreach ($condition->getValue1() as $value) {
                $cValue1[] = $this->convertValue($condition->getField(), $value);
            }
        } else {
            $cValue1 = $this->convertValue($condition->getField(), $condition->getValue1());
        }

        $cValue2 = $this->convertValue($condition->getField(), $condition->getValue2());

        return array($cField, $cValue1, $cValue2);
    }
}
