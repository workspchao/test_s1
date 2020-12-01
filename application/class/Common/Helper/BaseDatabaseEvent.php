<?php

namespace Common\Helper;

use Symfony\Component\EventDispatcher\Event;

class BaseDatabaseEvent extends Event
{

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
