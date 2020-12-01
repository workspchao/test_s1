<?php

namespace Common\Helper\CacheHelper;

interface CacheHelperInterface
{

    //for ip, username, password etc...
    public function setConfig(array $config);
    public function getConfig();

    //connect to cache server
    public function connect();

    //close connection from cache server
    public function close();

    public function setElasticache($key, $value, $expiry);
    public function getElasticache($key);
    public function deleteElasticache($key);
}
