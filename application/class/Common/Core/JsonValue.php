<?php

namespace Common\Core;

class JsonValue implements \Iterator, \Countable
{
    protected $value = array();   //array

    public function setJson($json)
    {
        $this->value = json_decode($json, true);
        $this->_mapKeys($this->value);
        return $this;
    }

    public function getJson()
    {
        if ($this->isNull())
            return null;

        return json_encode($this->value, JSON_UNESCAPED_SLASHES);
    }

    public function setArray(array $array)
    {
        $this->value = $array;
        $this->_mapKeys($this->value);
        return $this;
    }

    public function getArray()
    {
        return $this->value;
    }

    public function getValue($key)
    {
        if (array_key_exists($key, $this->value))
            return $this->value[$key];

        return null;
    }

    public function isNull()
    {
        return count($this) == 0;
    }

    public function equal(JsonValue $json)
    {
        $diff1 = array_diff($this->getArray(), $json->getArray());
        $diff2 = array_diff($json->getArray(), $this->getArray());

        return (count($diff1) <= 0 and count($diff2) <= 0);
    }

    //implementation
    protected $i = 0;
    protected $keys = array();

    protected function _mapKeys(array $array)
    {
        $this->keys = array_keys($array);   //map keys to index
    }

    public function current()
    {
        return $this->value[$this->key()];
    }

    public function key()
    {
        return $this->keys[$this->i];
    }

    public function next()
    {
        ++$this->i;
    }

    public function rewind()
    {
        $this->i = 0;
    }

    public function valid()
    {
        return array_key_exists($this->i, $this->keys);
    }

    public function count()
    {
        return count($this->value);
    }
}
