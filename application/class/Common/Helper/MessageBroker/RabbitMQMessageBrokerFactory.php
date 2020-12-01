<?php

namespace Common\Helper\MessageBroker;

use Common\Helper\MessageBroker\RabbitMQMessageBrokerFactory;

class RabbitMQMessageBrokerFactory
{

    public static function build($user_name = NULL, $password = NULL, $ip = NULL, $port = NULL)
    {
        $config = RabbitMQMessageBrokerFactory::_getConfig($user_name, $password, $ip, $port);

        return new RabbitMQMessageBroker($config);
    }

    private static function _getConfig($user_name = NULL, $password = NULL, $ip = NULL, $port = NULL)
    {
        $config = array();

        if (RabbitMQMessageBrokerFactory::_getUserName($user_name))
            $config['user_name'] = RabbitMQMessageBrokerFactory::_getUserName($user_name);

        if (RabbitMQMessageBrokerFactory::_getPassword($password))
            $config['password'] = RabbitMQMessageBrokerFactory::_getPassword($password);

        if (RabbitMQMessageBrokerFactory::_getIP($ip))
            $config['ip'] = RabbitMQMessageBrokerFactory::_getIP($ip);

        if (RabbitMQMessageBrokerFactory::_getPort($port))
            $config['port'] = RabbitMQMessageBrokerFactory::_getPort($port);

        return $config;
    }

    /*
     * Get the given config, otherwise get from environment variable
     */
    private static function _getUserName($user_name = NULL)
    {
        if ($user_name != NULL)
            return $user_name;

        if (getenv('MQ_USERNAME'))
            return getenv('MQ_USERNAME');

        return NULL;
    }

    private static function _getPassword($password = NULL)
    {
        if ($password != NULL)
            return $password;

        if (getenv('MQ_PASSWORD'))
            return getenv('MQ_PASSWORD');

        return NULL;
    }

    private static function _getIP($ip = NULL)
    {
        if ($ip != NULL)
            return $ip;

        if (getenv('MQ_IP'))
            return getenv('MQ_IP');

        return NULL;
    }

    private static function _getPort($port = NULL)
    {
        if ($port != NULL)
            return $port;

        if (getenv('MQ_PORT'))
            return getenv('MQ_PORT');

        return NULL;
    }
}
