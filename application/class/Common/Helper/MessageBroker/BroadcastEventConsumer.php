<?php

namespace Common\Helper\MessageBroker;

abstract class BroadcastEventConsumer extends EventConsumer
{

    public function listen($exchange_name, $routing_key, $queue_name = NULL)
    {
        if ($this->_message_broker->connect()) {
            $this->_message_broker->declare_exchange($exchange_name, 'fanout');
            $this->_message_broker->declare_queue($queue_name);

            $this->_message_broker->bind_queue($queue_name, $exchange_name, $routing_key);

            $this->_listen($queue_name);
        }
    }
}
