<?php

namespace Common\Helper\MessageBroker;

use PhpAmqpLib\Message\AMQPMessage;


abstract class EventProducer
{

    protected $_message_broker = NULL;
    protected $headers = NULL;
    protected $arguments = NULL;
    protected $exchange_type = 'direct';

    function __construct(MessageBrokerInterface $messageBroker = NULL)
    {
        if ($messageBroker == NULL) //this will use rabbitMQ message broker by default
        {
            $this->_message_broker = $this->getDefaultMQ();
        } else
            $this->_message_broker = $messageBroker;
    }

    public function setExchangeType($type)
    {
        $this->exchange_type = $type;
        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    protected function getDefaultMQ()
    {
        return RabbitMQMessageBrokerFactory::build();
    }

    public function trigger($exchange_name, $routing_key, $data)
    {
        if ($this->_message_broker->connect()) {
            $this->_message_broker->declare_exchange($exchange_name, $this->exchange_type, $this->arguments);
            $this->_message_broker->publish($data, $exchange_name, $routing_key, AMQPMessage::DELIVERY_MODE_PERSISTENT, $this->headers);

            $this->_message_broker->disconnect();
        }
    }

    public function triggerWithoutDeclaration($exchange_name, $routing_key, $data)
    {
        if ($this->_message_broker->connect()) {
            //just listen
            $this->_message_broker->publish($data, $exchange_name, $routing_key, AMQPMessage::DELIVERY_MODE_PERSISTENT, $this->headers);
        }
    }
}
