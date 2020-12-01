<?php

namespace Common\Helper\RocketMessageBroker;

use Common\Core\IpAddress;
use Common\Helper\DateTimeHelper;
use MQ\Exception\MessageNotExistException;
use MQ\Exception\AckMessageException;

abstract class EventConsumer {

    protected $_message_consumer = NULL;
    protected $start_time;
    protected $ttl = 1; //1s
    protected $force_ack = true;
    protected $updatedBy;
    protected $ipAddress;
    protected $topic = NULL;
    protected $tag = NULL;

    public function setForceAcknowledgement($force) {
        if ($force === false)
            $this->force_ack = false;
        else
            $this->force_ack = true;

        return $this;
    }

    public function getForceAcknowledgement() {
        return $this->force_ack;
    }

    function __construct($topic, $tag = NULL, $groupId = 'GID_TEST_S1YYWZ', $ttl = 10) {
        $this->_message_consumer = RocketMQConsumerFactory::build($topic, $groupId, $tag);
        $this->start_time = DateTimeHelper::toUnix(DateTimeHelper::getNow());
        $this->setTTL($ttl);  //set to default ttl
        $this->topic = $topic;
        $this->tag = $tag;
    }

    abstract protected function doTask($msg);

    public function setTTL($ttl) {
        $this->ttl = $ttl;
        return true;
    }

    public function getTTL() {
        return $this->ttl;
    }

    protected function isWithinTTL() {
        if ($this->getTTL() == NULL)
            return true;

        $now = DateTimeHelper::toUnix(DateTimeHelper::getNow());
        return !($now - $this->start_time >= $this->getTTL());
    }

    public function listen() {

        while ($this->isWithinTTL()) {
            try {
                $messages = $this->_message_consumer->consumeMessage(
                        3, // 一次最多消费3条(最多可设置为16条)
                        30 // 长轮询时间3秒（最多可设置为30秒）
                );

                $ack = false;

                //if force ack, then always ack
                if ($this->getForceAcknowledgement())
                    $ack = true;
                    
                $receiptHandles = array();
                foreach ($messages as $message) {
                    // $sTime = microtime(true);
                    // log_message('debug', 'start consume message: '.$sTime);
                    if ($this->doTask($message)){
                        $ack = true;
                    }

                    // $eTime = microtime(true);
                    // log_message('debug', 'finish consume one message: '.$eTime);
                    // log_message('debug', '耗时: '. ($eTime - $sTime));
                    
                    if($ack){
                        $receiptHandles[] = $message->getReceiptHandle();
                    }
                }
                
                //确认消息
                if (!empty($receiptHandles))
                    $this->_message_consumer->ackMessage($receiptHandles);
            }
            catch (MessageNotExistException $e) {
                // 没有消息可以消费
                log_message("info", "{$this->topic} {$this->tag} No message, contine long polling!RequestId:" . $e->getRequestId());
                // usleep(200);
                continue;
            }
            catch (AckMessageException $e) {
                // 某些消息的句柄可能超时了会导致确认不成功
                log_message("error", "{$this->topic} {$this->tag} Ack Error, RequestId:" . $e->getRequestId());
                foreach ($e->getAckMessageErrorItems() as $errorItem) {
                    log_message("error", "\tReceiptHandle: ". $errorItem->getReceiptHandle() .", ErrorCode:". $errorItem->getErrorCode() .", ErrorMsg:". $errorItem->getErrorCode() ."\n");
                }
                // usleep(200);
                continue;
            }
            catch (\Exception $e) {
                if ($e instanceof MessageNotExistException) {
                    // 没有消息可以消费
                    log_message("info", "{$this->topic} {$this->tag} No message, contine long polling!RequestId:" . $e->getRequestId());
                    // usleep(200);
                    continue;
                }

                if ($e instanceof AckMessageException) {
                    // 某些消息的句柄可能超时了会导致确认不成功
                    log_message("error", "{$this->topic} {$this->tag} Ack Error, RequestId:" . $e->getRequestId());
                    foreach ($e->getAckMessageErrorItems() as $errorItem) {
                        log_message("error", "\tReceiptHandle: ". $errorItem->getReceiptHandle() .", ErrorCode:". $errorItem->getErrorCode() .", ErrorMsg:". $errorItem->getErrorCode() ."\n");
                    }
                    // usleep(200);
                    continue;
                }
                log_message("error", "EventConsumer - catch Exception: {$this->topic} {$this->tag}" . $e->getMessage());
            }
            // usleep(500);
        }

        return true;
    }

    public function setUpdatedBy($updatedBy) {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function getUpdatedBy() {
        return $this->updatedBy;
    }

    public function setIpAddress(IpAddress $ip) {
        $this->ipAddress = $ip;
        return $this;
    }

    public function getIpAddress() {
        return $this->ipAddress;
    }

}
