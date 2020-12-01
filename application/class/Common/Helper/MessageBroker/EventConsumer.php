<?php

namespace Common\Helper\MessageBroker;

use Common\Core\IpAddress;
use Common\Helper\DateTimeHelper;

abstract class EventConsumer
{

    protected $_message_broker = NULL;
    protected $start_time;
    protected $ttl = 10; //10s

    protected $force_ack = true;
    protected $updatedBy;
    protected $ipAddress;

    public function setForceAcknowledgement($force)
    {
        if ($force === false)
            $this->force_ack = false;
        else
            $this->force_ack = true;

        return $this;
    }

    public function getForceAcknowledgement()
    {
        return $this->force_ack;
    }

    function __construct(MessageBrokerInterface $messageBroker = NULL, $ttl = 10)
    {
        if ($messageBroker == NULL) //this will use rabbitMQ message broker by default
        {
            $this->_message_broker = $this->getDefaultMQ();
        } else
            $this->_message_broker = $messageBroker;

        $this->start_time = DateTimeHelper::toUnix(DateTimeHelper::getNow());
        $this->setTTL($ttl);  //set to default ttl
    }

    protected function getDefaultMQ()
    {
        return RabbitMQMessageBrokerFactory::build();
    }

    abstract protected function doTask($msg);

    public function setTTL($ttl)
    {
        $this->ttl = $ttl;
        return true;
    }

    public function getTTL()
    {
        return $this->ttl;
    }

    protected function isWithinTTL()
    {
        if ($this->getTTL() == NULL)
            return true;

        $now = DateTimeHelper::toUnix(DateTimeHelper::getNow());
        return !($now - $this->start_time >= $this->getTTL());
    }

    public function listen($exchange_name, $routing_key, $queue_name = NULL)
    {
        if ($this->_message_broker->connect()) {
            //declare
            $this->_message_broker->declare_exchange($exchange_name, 'direct');
            $this->_message_broker->declare_queue($queue_name);

            if ($queue_name == NULL)
                $queue_name = $this->_message_broker->bind_random_queue($exchange_name, $routing_key);
            else
                $this->_message_broker->bind_queue($queue_name, $exchange_name, $routing_key);

            //and listen
            $this->_listen($queue_name);
        }
    }

    public function listenWithoutDeclaration($queue_name)
    {
        if ($this->_message_broker->connect()) {
            //just listen
            $this->_listen($queue_name);
        }
    }

    protected function _listen($queue_name)
    {
        $callback = function ($msg) {
            $ack = false;
            if ($this->doTask($msg))
                $ack = true;

            //if force ack, then always ack
            if ($this->getForceAcknowledgement())
                $ack = true;

            if ($ack)
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $tag = $this->_message_broker->consume($queue_name, $callback);

        while ($this->isWithinTTL()) {
            if (!$this->_message_broker->isConnected())
                break; //terminate if connection dropped

            if (!$this->_message_broker->wait())
                break;
        }

        try { //try to disconnect just in case
            $this->_message_broker->cancel_consume($tag);
            $this->_message_broker->disconnect();
        } catch (\Exception $ex) { }
    }

    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    public function setIpAddress(IpAddress $ip)
    {
        $this->ipAddress = $ip;
        return $this;
    }

    public function getIpAddress()
    {
        return $this->ipAddress;
    }
}
