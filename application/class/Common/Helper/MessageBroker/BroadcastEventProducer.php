<?php

namespace Common\Helper\MessageBroker;

abstract class BroadcastEventProducer extends EventProducer
{

    public function trigger($exchange_name, $routing_key, $data)
    {
        if ($this->_message_broker->connect()) {
            $this->_message_broker->declare_exchange($exchange_name, 'fanout');
            $this->_message_broker->publish($data, $exchange_name, NULL);

            $this->_message_broker->disconnect();
        }
    }
}
