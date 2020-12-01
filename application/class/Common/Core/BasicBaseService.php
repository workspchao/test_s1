<?php

namespace Common\Core;

use Common\Helper\CacheHelper\CacheHelperFactory;
//use Common\AuditLog\AuditLogEventProducer;
//use Common\AuditLog\AuditLogAction;

abstract class BasicBaseService
{

    protected $ipAddress = NULL;
    protected $updatedBy = NULL;
    protected $responseCode = NULL;
    protected $responseMessage = NULL;
    protected $benchmark = null;
    protected $benchmark_fun = null;

    protected $user_agent = '';

    function __construct($ipAddress = '127.0.0.1', $updatedBy = NULL)
    {

        $this->setIpAddress(IpAddress::fromString($ipAddress));
        $this->setUpdatedBy($updatedBy);
    }

    public function setUserAgent($user_agent){
        $this->user_agent = $user_agent;
        return $this;
    }
    
    public function getUserAgent(){
        return $this->user_agent;
    }

    public function setIpAddress(IpAddress $ip)
    {
        $this->ipAddress = $ip;
        return true;
    }

    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return true;
    }

    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    
    public function setBenchmark($benchmark){
        $this->benchmark = $benchmark;
        return $this;
    }
    
    public function getBenchmark(){
        return $this->benchmark;
    }
    
    public function setBenchmarkFun($benchmark_fun){
        $this->benchmark_fun = $benchmark_fun;
        return $this;
    }
    
    public function getBenchmarkFun(){
        return $this->benchmark_fun;
    }
    
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
        return true;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }

    public function setResponseMessage($message)
    {
        $this->responseMessage = $message;
        return $this;
    }

    public function getResponseMessage()
    {
        return $this->responseMessage;
    }
    
//
//    protected function _getLogRoutingKey()
//    {
//        if (getEnv('LOG_ROUTING_KEY'))
//            return getEnv('LOG_ROUTING_KEY');
//
//        return false;
//    }
//
//    protected function fireLogEvent($tableName, $action, $id, BaseEntity $oriData = NULL, $header_id = NULL)
//    {
//        if (!$routingKey = $this->_getLogRoutingKey()) {
//            error_log('No Routing Key Defined');
//            return false;
//        }
//
//        $auditLog = new AuditLogEventProducer($tableName, $this->getIpAddress());
//
//        if ($oriData != NULL) {
//            $auditLog->setOldValue($oriData);
//        }
//
//        if ($action == AuditLogAction::DELETE)
//            $newData = $this->getRepository()->findById($id, true);
//        else
//            $newData = $this->getRepository()->findById($id);
//
//        if ($header_id != NULL) {
//            $auditLog->setHeaderId($header_id);
//        }
//
//        if ($newData) {
//            $auditLog->setNewValue($newData);
//            $auditLog->setIpAddress($this->getIpAddress());
//            $auditLog->setActionType($action);
//            $auditLog->sendLogEvent($routingKey);
//        }
//    }

    /*
     * Service layer can access to cache as well
     */

    /**
     * 
     * @param type $key
     * @param type $value
     * @param type $expiry(in seconds)
     * @return boolean
     */
    public function setElasticCache($key, $value, $expiry = 864000)
    {
        try {
            $cache = CacheHelperFactory::build();
            return $cache->setElasticache($key, $value, $expiry);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 
     * @param type $key
     * @return boolean
     */
    public function getElasticCache($key)
    {
        try {
            $cache = CacheHelperFactory::build();
            return $cache->getElasticache($key);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 
     * @param type $key
     * @return boolean
     */
    public function deleteElastiCache($key)
    {
        try {
            $cache = CacheHelperFactory::build();
            return $cache->deleteElasticache($key);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    protected function log_benchmark($start, $mark, $fun, $action){
        if(in_array(ENVIRONMENT, array('local', 'development', 'testing'))){
            
            if($this->benchmark_fun){
                $fun = $this->benchmark_fun;
            }
            
            $this->benchmark->mark($mark);
            $elapsed_time = $this->benchmark->elapsed_time($start, $mark);
            $elapsed_time = number_format($elapsed_time, 6);
            log_message("debug", "$fun - use: $elapsed_time - $action");
        }
    }
}
