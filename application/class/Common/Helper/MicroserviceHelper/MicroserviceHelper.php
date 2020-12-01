<?php

namespace Common\Helper\MicroserviceHelper;

use Common\Helper\HttpService;
use Common\Helper\ResponseMessage;

class MicroserviceHelper
{

    //use restAPI to communicate
    protected $_http_service;
    protected $config;

    function __construct(array $config)
    {
        $this->_http_service = new HttpService();
        $this->setConfig($config);

        if (!$this->_getBaseUrl())
            throw new \exception('Invalid Base Url');

        $this->_http_service->setUrl($this->_getBaseUrl());
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        return true;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function _getBaseUrl()
    {
        if (isset($this->getConfig()['base_url']))
            return $this->getConfig()['base_url'];

        return false;
    }
    
    public function call(array $option)
    {
        $method = $this->_readOption($option, 'method');
        $uri = $this->_readOption($option, 'uri');
        $param = $this->_readOption($option, 'param');
        $header = $this->_readOption($option, 'header') ? $this->_readOption($option, 'header') : array();

        if (
            $method !== false and
            $uri !== false
        ) {
            if (!$param)
                $param = array();

            $response = false;
            switch ($method) {
                case 'post':
                    $response = $this->_callPost($uri, $param, $header);
                    break;
                case 'get':
                    $response = $this->_callGet($uri, $param, $header);
                    break;
                case 'delete':
                    $response = $this->_http_service->delete($header, $param, $uri);
                    break;
            }

            if ($response !== false) {
                return $this->_extractResponse($response);
            }
        }

        return false;
    }
    
    public function getLastStatus(){
        return $this->_http_service->getLastStatus();
    }
    
    public function getLastReponse()
    {
        return $this->_http_service->getLastResponse();
    }

    protected function _extractResponse(ResponseMessage $msg)
    {
        return $msg->getMessage()['message'];
    }

    protected function _readOption(array $option, $key)
    {
        if (isset($option[$key]))
            return $option[$key];

        return false;
    }

    protected function _callPost($uri, array $param, array $header = array())
    {
        return $this->_http_service->post($header, $param, $uri);
    }

    protected function _callGet($uri, array $param, array $header = array())
    {
        return $this->_http_service->get($header, $param, $uri);
    }
}
