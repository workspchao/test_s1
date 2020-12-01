<?php

namespace Common\Core;

use Common\Microservice\AccountService\UserEntity;

class BaseEntityCollection implements \Iterator, \Countable, \JsonSerializable {

    protected $data = array();
    protected $indexedData = array();
    //private $key_mapper = array();
    private $position = 0;

    function __construct() {
        //by default, id is indexed field
        $this->indexedData['id'] = array();
    }

    /** @return void */
    public function addData(BaseEntity $entity) {
        $this->data[] = $entity;

        //reindex fields
        $tmpArray = $entity->jsonSerialize();

        foreach ($this->indexedData as $field => $reference) {
            if (array_key_exists($field, $tmpArray) && $tmpArray[$field] != NULL && !empty($tmpArray[$field])) {
                $this->indexedData[$field][$tmpArray[$field]] = $entity;
            }
        }

        $tmpArray = NULL;
    }

    /** @return boolean */
    public function removeDataById($id) {
        $key = $this->findKeyById($id);
        if ($key !== FALSE) {
            unset($this->data[$key]);
            unset($this->indexedData['id'][$id]);
            return true;
        }

        return false;
    }

    public function replaceElement(BaseEntity $entity) {
        $key = $this->findKeyById($entity->getId());
        if ($key !== FALSE) {
            $this->data[$key] = $entity;
            $this->indexedData['id'][$entity->getId()] = $entity;
        } else
            $this->addData($entity);

        return $this;
    }

    public function indexField($field) {
        if ($this->IndexExists($field))
            $this->indexedData[$field] = NULL;  //reset

        foreach ($this as $entity) {
            $tmpArray = $entity->jsonSerialize();
            if (array_key_exists($field, $tmpArray) && $tmpArray[$field] != NULL && !empty($tmpArray[$field])) {
                $this->indexedData[$field][$tmpArray[$field]] = $entity;
            }

            $tmpArray = NULL;
        }
    }

    public function IndexExists($field) {
        return array_key_exists($field, $this->indexedData);
    }

    public function getFromIndex($field, $key) {
        if ($this->IndexExists($field) and array_key_exists($key, $this->indexedData[$field]))
            return $this->indexedData[$field][$key];

        return false;
    }

    /** @return boolean */
    private function findKeyById($id) {
        if ($id != NULL) {
            foreach ($this->data as $key => $data) {
                if ($data->getId() === $id) {
                    return $key;
                }
            }
        }

        return false;
    }

    /** @return array */
    public function toArray() {
        return iterator_to_array($this);
    }

    // === interface \Iterator ======================================
    function rewind() {
        $this->position = 0;
    }

    function current() {
        //$key = $this->key_mapper[$this->position];
        return $this->data[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->data[$this->position]);
    }

    // === interface \Countable ======================================

    /** @return int */
    public function count() {
        return count($this->data);
    }

    public function getSelectedField(array $fields) {
        $new_collection = array();
        foreach ($this as $entity) {
            $new_collection[] = $entity->getSelectedField($fields);
        }

        return $new_collection;
    }

    public function jsonSerialize() {
        $json = array();
        foreach ($this as $entity) {
            $json[] = $entity->jsonSerialize();
        }

        return $json;
    }

    public function getById($id) {
        return $this->getFromIndex('id', $id);
    }

    public function getFirstByFieldValue($field, $value) {
        if (!$this->IndexExists($field))
            $this->indexField($field);

        return $this->getFromIndex($field, $value);
    }

    public function getCollectionByFieldValue($field, $value) {
        $collection = new static;
        foreach ($this as $entity) {
            $tmpArray = $entity->jsonSerialize();
            if (array_key_exists($field, $tmpArray) && $tmpArray[$field] != NULL && !empty($tmpArray[$field])) {
                if(is_array($value)){
                    if (in_array($tmpArray[$field], $value))
                        $collection->addData($entity);
                }
                else{
                    if ($tmpArray[$field] == $value)
                        $collection->addData($entity);
                }
            }

            $tmpArray = NULL;
        }

        return $collection;
    }

    public function joinCreatorName(BaseEntityCollection $userCollection) {
        if ($userCollection) {
            foreach ($this as $entity) {
                if ($user = $userCollection->getById($entity->getCreatedBy())) {
                    //if ($user instanceof UserEntity) {
                        $entity->setCreatedByName($user->getName());
                    //}
                }
            }
        }

        return $this;
    }

    public function joinUpdaterName(BaseEntityCollection $userCollection) {
        if($userCollection){
            foreach ($this as $entity) {
                if ($user = $userCollection->getById($entity->getUpdatedBy())) {
                    //if ($user instanceof UserEntity) {
                        $entity->setUpdatedByName($user->getName());
                    //}
                }
            }
        }

        return $this;
    }

    public function getIds() {
        return array_keys($this->indexedData['id']);
    }

    public function getFieldValues($field) {
        $values = array();

        foreach ($this as $entity) {
            $tmpArray = $entity->jsonSerialize();
            if (array_key_exists($field, $tmpArray) && $tmpArray[$field] != NULL && !empty($tmpArray[$field])) {
                $tmpValue = $tmpArray[$field];
                if(!in_array($tmpValue, $values)){
                    $values[] = $tmpValue;
                }
            }

            $tmpArray = NULL;
        }

        return $values;
    }

    public function pagination($limit = NULL, $page = NULL) {
        $total = count($this);

        $result = new PaginatedResult();
        $result->setTotal($total);

        if ($limit and $page) {
            $start = ($page - 1) * $limit;
            $end = $start + $limit - 1;

            $paginatedCollection = new static();
            for ($i = $start; $i <= $end; $i++) {
                $this->position = $i;
                if ($this->valid())
                    $paginatedCollection->addData($this->current());
                else
                    break;
            }

            $result->setResult($paginatedCollection);
        } else {
            $result->setResult($this);
        }

        return $result;
    }

    public function toPaginatedResult() {
        $result = new PaginatedResult();
        $result->setResult($this);
        $result->setTotal(count($this));

        return $result;
    }

}
