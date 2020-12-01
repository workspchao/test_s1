<?php

namespace Common\Helper\CacheHelper;

use Common\Core\Exception\CacheConnectionException;

class CacheHelper implements CacheHelperInterface
{

    private $config = NULL;
    private $client = NULL;

    function __construct(array $config)
    {
        $this->setConfig($config);
        if (!$this->_validateConfig()) {
            throw new CacheConnectionException('invalid config');
        }

        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            //PHP 5.3 with php-pecl-memcache
            $this->client = new \Memcache();
        } else {
            //PHP 5.4 with php54-pecl-memcached:
            $this->client = new \Memcached();
        }
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function connect()
    {
        $end_point = $this->_getServerEndpoint();
        $port = $this->_getServerPort();

        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            //PHP 5.3 with php-pecl-memcache
            return $this->client->connect($end_point, $port);
        } else {
            //PHP 5.4 with php54-pecl-memcached:
            return $this->client->addServer($end_point, $port);
        }
    }

    //close connection from cache server
    public function close()
    {
        $this->client->quit();
    }

    public function setElasticache($key, $value, $expiry = 864000)
    {
        $this->connect();
        $result = $this->client->set($key, $value, $expiry);
        $this->close();

        return $result;
    }

    public function getElasticache($key)
    {
        $this->connect();
        $result = $this->client->get($key);
        $this->close();

        return $result;
    }

    public function deleteElasticache($key)
    {
        $this->connect();
        $result = $this->client->delete($key);
        $this->close();

        return $result;
    }

    private function _validateConfig()
    {
        return ($this->_getServerEndpoint() and
            $this->_getServerPort());
    }

    private function _getServerEndpoint()
    {
        if (isset($this->getConfig()['server_endpoint']))
            return $this->getConfig()['server_endpoint'];

        return false;
    }

    private function _getServerPort()
    {
        if (isset($this->getConfig()['server_port']))
            return $this->getConfig()['server_port'];

        return false;
    }
}
