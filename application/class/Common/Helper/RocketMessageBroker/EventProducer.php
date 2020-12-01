<?php

namespace Common\Helper\RocketMessageBroker;

use MQ\Model\TopicMessage;
use Common\Helper\RocketMessageBroker\RocketMQProducerFactory;

abstract class EventProducer {

    protected $_message_producer = NULL;

    function __construct($topic) {

        $this->_message_producer = RocketMQProducerFactory::build($topic);
    }

    public function publishMessage($content, $tag = NULL) {
        try {
            $publishMessage = new TopicMessage(
                    $content// 消息内容
            );

            if (!empty($tag)) {
                //设置tag
                $publishMessage->setMessageTag($tag);
            }

            $result = $this->_message_producer->publishMessage($publishMessage);

            if ($result) {
                return $result->getMessageId();
            }

            log_message("error", "EventProducer - publishMessage - fail - " . json_encode($publishMessage));
            return false;
        }
        catch (\Exception $e) {
            //print_r($e->getMessage() . "\n");
            log_message("error", "EventProducer - publishMessage - catch exception:" . $e->getMessage());
            return false;
        }
    }

}
