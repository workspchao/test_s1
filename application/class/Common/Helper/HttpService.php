<?php

namespace Common\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;

class HttpService
{

    private $http_client;
    private $url;
    private $response_message;

    public function __construct()
    {
        $this->http_client = new Client();
        $this->response_message = new ResponseMessage();
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getLastStatus()
    {
        return $this->response_message->getStatusCode();
    }

    public function getLastResponse($is_array = true)
    {
        if ($is_array) {
            $last_response = array();
            if ($x = $this->response_message->getMessage()['message']) {
                if (is_object($x)) {
                    $last_response = (array) $x;
                } elseif (is_array($x)) {
                    $last_response = $x;
                }
            }
        } else {
            $last_response = $this->response_message->getMessage()['message'];
        }

        return $last_response;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function post(array $header, $param, $uri = NULL, $param_field = 'form_params')
    {
        return $this->request('POST', $header, $param, $uri, $param_field);
    }

    public function get(array $header, array $param, $uri = NULL)
    {
        return $this->request('GET', $header, $param, $uri, 'query');
    }

    public function delete(array $header, $param, $uri = NULL, $param_field = 'form_params')
    {
        return $this->request('DELETE', $header, $param, $uri, $param_field);
    }

    public function request($method, array $header, array $param, $uri = NULL, $param_field = 'form_params')
    {
        $options['headers'] = $header;
        $options[$param_field] = $param;
        $options['version'] = '1.2';
        return $this->_request($method, $options, $uri);
    }

    protected function _request($method, $options, $uri = NULL)
    {
        try {
            $url = $this->getUrl() . $uri;
            log_message("debug", "_request start - $method $url " . json_encode($options));
            $response = $this->http_client->request($method, $url, $options);
            if($resp = $this->_returnResponse($response)){
                log_message("debug", "_request end (success) - $method $url " . $resp->getJsonMessage());
            }
            else{
                log_message("debug", "_request end (fail) - $method $url " . json_encode($resp));
            }
            return $resp;
        }
        catch (ClientException $e) {
            //400
            error_log('HTTP Service - ClientException - ' . $e->getMessage());
            log_message("error", 'HTTP Service - ClientException - ' . $e->getMessage());
            $response = $e->getResponse();
            return $this->_returnResponse($response);
        }
        catch (RequestException $e) {
            //connect timeout
            error_log('HTTP Service - RequestException - ' . $e->getMessage());
            log_message("error", 'HTTP Service - RequestException - ' . $e->getMessage());
            $response = $e->getResponse();
            return $this->_returnResponse($response);
        } catch (ServerException $e) {
            error_log('HTTP Service - ServerException - ' . $e->getMessage());
            log_message("error", 'HTTP Service - RequestException - ' . $e->getMessage());
            return $this->_returnException($e);
        } catch (TransferException $e) {
            error_log('HTTP Service - TransferException - ' . $e->getMessage());
            log_message("error", 'HTTP Service - RequestException - ' . $e->getMessage());
            return $this->_returnException($e);
        } catch (\Exception $e) {
            error_log('HTTP Service - Exception - ' . $e->getMessage());
            log_message("error", 'HTTP Service - RequestException - ' . $e->getMessage());
            return $this->_returnException($e);
        }

        return false;
    }

    protected function _returnResponse($response)
    {
        $this->response_message = new ResponseMessage();

        if (!($response instanceof Response))
            return false;

        if ($responseMsg =  GuzzleWrapper::response($response)) {
            $this->response_message = $responseMsg;
            if ($this->response_message->getStatusCode() == 200) {
                return $this->response_message;
            }
        }

        return false;
    }

    private function _returnException($e)
    {
        $this->response_message = new ResponseMessage();
        return GuzzleWrapper::exception($e);
    }
}
