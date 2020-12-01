<?php

namespace Common\Helper\MessageBroker;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Common\Core\Exception\MessageBrokerConnectionException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQMessageBroker implements MessageBrokerInterface
{

    protected $_connection = NULL;
    protected $_channel = NULL;
    private $config = NULL;
    protected $_timeout = 1;   //1s

    function __construct(array $config)
    {
        $this->setConfig($config);
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
        return $this;
    }

    public function connect()
    {
        if (!$this->_validate_config()) {
            throw new MessageBrokerConnectionException('Invalid Configuration');
        }

        $user_name = $this->_get_user_name();
        $password = $this->_get_password();
        $ip = $this->_get_ip();
        $port = $this->_get_port();

        try {
            $this->_connection = new AMQPStreamConnection($ip, $port, $user_name, $password);
            $this->_channel = $this->_connection->channel();
            return ($this->_channel != NULL);
        } catch (\Exception $e) {
            error_log('RabbitMQ Connection Error');
            return false;
        }
    }

    private function _validate_config()
    {
        return ($this->_get_user_name() and
            $this->_get_password() and
            $this->_get_ip() and
            $this->_get_port());
    }

    private function _get_user_name()
    {
        if (isset($this->getConfig()['user_name']))
            return $this->getConfig()['user_name'];

        return false;
    }

    private function _get_password()
    {
        if (isset($this->getConfig()['password']))
            return $this->getConfig()['password'];

        return false;
    }

    private function _get_ip()
    {
        if (isset($this->getConfig()['ip']))
            return $this->getConfig()['ip'];

        return false;
    }

    private function _get_port()
    {
        if (isset($this->getConfig()['port']))
            return $this->getConfig()['port'];

        return false;
    }

    public function disconnect()
    {
        $this->_channel->close();
        $this->_connection->close();
    }

    public function declare_queue($queue_name)
    { //add durable queue
        $this->_channel->queue_declare($queue_name, false, true, false, false);
    }

    public function declare_undurable_queue($queue_name)
    { //add undurable queue
        $this->_channel->queue_declare($queue_name, false, false, false, false);
    }

    public function get_random_queue()
    {
        return $this->_channel->queue_declare("");
    }

    public function bind_random_queue($exhange_name, $routing_key)
    {
        list($queue_name,,) = $this->get_random_queue();
        $this->bind_queue($queue_name, $exhange_name, $routing_key);

        return $queue_name;
    }

    public function bind_queue($queue_name, $exhange_name, $routing_key)
    {
        $this->_channel->queue_bind($queue_name, $exhange_name, $routing_key);
    }

    public function declare_exchange($exchange_name, $exchange_type = 'direct', array $arguments = null)
    { //add durable exchange
        $this->_channel->exchange_declare($exchange_name, $exchange_type, false, true, false, false, false, $arguments);
    }

    public function isBusy()
    {
        return count($this->_channel->callbacks);
    }

    public function wait()
    {
        try {
            $this->_channel->wait(null, false, $this->_timeout);
            return true;
        } catch (AMQPTimeoutException $e) { //timeout, this is normal
            return true;
        } catch (\Exception $e) { //something else happened       
            return false;
        }
    }

    public function publish($data, $exchange_name, $routing_key, $delivery_mode = AMQPMessage::DELIVERY_MODE_PERSISTENT, array $headers = null)
    {
        $msg = new AMQPMessage($data, array('delivery_mode' => $delivery_mode));
        if ($headers != null) {
            $msg->set('application_headers', $this->_constructHeader($headers));
        }

        $this->_channel->basic_publish($msg, $exchange_name, $routing_key);
    }

    protected function _constructHeader(array $headers)
    {
        return new AMQPTable($headers);
    }

    public function consume($queue_name, $callback)
    {
        return $this->_channel->basic_consume($queue_name, '', false, false, false, false, $callback);
    }

    public function acknowledge($tag)
    {
        return $this->_channel->basic_ack($tag);
    }

    public function cancel_consume($consumer_tag)
    {
        $this->_channel->basic_cancel($consumer_tag);
    }

    public function isConnected()
    {
        return $this->_connection->isConnected();
    }
}
