<?php

// require "vendor/autoload.php";
namespace Common\Helper\RocketMessageBroker;
use MQ\MQClient;

class RocketMQProducerFactory
{   
    public static function build($topic)
    {
        
        $client = new MQClient(
            // 设置HTTP接入域名（此处以公共云生产环境为例）
            getenv('ROCKET_MQ_POINT'),
            // AccessKey 阿里云身份验证，在阿里云服务器管理控制台创建
            getenv('ROCKET_MQ_CLOUD_KEY'),
            // SecretKey 阿里云身份验证，在阿里云服务器管理控制台创建
            getenv('ROCKET_MQ_CLOUD_SECRET')
        );

        // 所属的 Topic
        // Topic所属实例ID，默认实例为空NULL
        $instanceId = getenv("ROCKET_MQ_INSTANCEID");

        $producer = $client->getProducer($instanceId, $topic);


        return $producer;
    }
}
