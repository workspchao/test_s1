<?php

namespace Common\Core;

use Common\Helper\ArrayExtractor;


abstract class BaseEntity implements \JsonSerializable
{

    protected $id;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted_at;
    protected $deleted_by;
    protected $created_by_name;
    protected $updated_by_name;
    protected $created_from;
    protected $created_to;

    function __construct()
    {
        $this->created_at = new BaseDateTime();
        $this->updated_at = new BaseDateTime();
        $this->deleted_at = new BaseDateTime();

        $this->created_from = new BaseDateTime();
        $this->created_to = new BaseDateTime();
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setCreatedAt(BaseDateTime $dt)
    {
        $this->created_at = $dt;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
        return $this;
    }

    public function getCreatedBy()
    {
        return $this->created_by;
    }

    public function setUpdatedAt(BaseDateTime $dt)
    {
        $this->updated_at = $dt;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedBy($updated_by)
    {
        $this->updated_by = $updated_by;
        return $this;
    }

    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    public function setDeletedAt(BaseDateTime $dt)
    {
        $this->deleted_at = $dt;
        return $this;
    }

    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    public function setDeletedBy($deleted_by)
    {
        $this->deleted_by = $deleted_by;
        return $this;
    }

    public function getDeletedBy()
    {
        return $this->deleted_by;
    }

    public function setCreatedByName($created_by_name)
    {
        $this->created_by_name = $created_by_name;
        return $this;
    }

    public function getCreatedByName()
    {
        return $this->created_by_name;
    }

    public function setUpdatedByName($updated_by_name)
    {
        $this->updated_by_name = $updated_by_name;
        return $this;
    }

    public function getUpdatedByName()
    {
        return $this->updated_by_name;
    }
    
    public function setCreatedFrom(BaseDateTime $dt)
    {
        $this->created_from = $dt;
        return $this;
    }

    public function getCreatedFrom()
    {
        return $this->created_from;
    }

    public function setCreatedTo(BaseDateTime $dt)
    {
        $this->created_to = $dt;
        return $this;
    }

    public function getCreatedTo()
    {
        return $this->created_to;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'created_at' => $this->getCreatedAt() ? $this->getCreatedAt()->getString() : "",
            'created_by' => $this->getCreatedBy(),
            'updated_at' => $this->getUpdatedAt() ? $this->getUpdatedAt()->getString() : "",
            'updated_by' => $this->getUpdatedBy(),
            'deleted_at' => $this->getDeletedAt() ? $this->getDeletedAt()->getString() : "",
            'deleted_by' => $this->getDeletedBy(),
            'created_by_name' => $this->getCreatedByName(),
            'updated_by_name' => $this->getUpdatedByName()

        ];
    }

    public function getSelectedField(array $fields)
    {
        return ArrayExtractor::extract($this->jsonSerialize(), $fields);
    }
}
