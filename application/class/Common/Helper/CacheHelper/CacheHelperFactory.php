<?php

namespace Common\Helper\CacheHelper;

use Common\Helper\CacheHelper\CacheHelper;

class CacheHelperFactory
{
    protected static $_instance;
    
    public static function build($endpoint = NULL, $port = NULL)
    {
        if (CacheHelperFactory::$_instance == NULL) {
            $config = CacheHelperFactory::_getConfig($endpoint, $port);
            CacheHelperFactory::$_instance = new CacheHelper($config);
        }

        return CacheHelperFactory::$_instance;
    }

    private static function _getConfig($endpoint = NULL, $port = NULL)
    {
        $config = array();

        if (CacheHelperFactory::_getEndPoint($endpoint))
            $config['server_endpoint'] = CacheHelperFactory::_getEndPoint($endpoint);

        if (CacheHelperFactory::_getPort($port))
            $config['server_port'] = CacheHelperFactory::_getPort($port);

        return $config;
    }

    /*
     * Get the given config, otherwise get from environment variable
     */
    private static function _getEndPoint($endpoint = NULL)
    {
        if ($endpoint != NULL)
            return $endpoint;

        if (getenv('MEMCACHE_SERVER_ENDPOINT'))
            return getenv('MEMCACHE_SERVER_ENDPOINT');

        return NULL;
    }

    private static function _getPort($port = NULL)
    {
        if ($port != NULL)
            return $port;

        if (getenv('MEMCACHE_SERVER_PORT'))
            return getenv('MEMCACHE_SERVER_PORT');

        return NULL;
    }
}
