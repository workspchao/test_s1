<?php

namespace Common\Core;

class PaginatedResult
{

    //put public so that the existing will still be able to use
    public $result;
    public $total = 0;

    function __construct()
    {
        $this->result = new BaseEntityCollection();
    }

    //result can be anything, array, collection
    public function setResult($collection)
    {
        $this->result = $collection;
        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function combineCollection(BaseEntityCollection $collection)
    {
        foreach ($collection as $entity) {
            $this->combineEntity($entity);
        }

        return $this;
    }

    public function combineEntity(BaseEntity $entity)
    {
        if ($this->getResult() instanceof BaseEntityCollection) {
            $this->setTotal($this->getTotal() + 1);
            $this->getResult()->addData($entity);
        }

        return $this;
    }
}
