<?php

namespace Common\Helper;

use GuzzleHttp\Psr7\Response;

class GuzzleWrapper
{
    public static function response(Response $response)
    {
        $ir = new ResponseMessage();
        try {
            $ir->setStatusCode($response->getStatusCode());

            foreach ($response->getHeaders() as $key => $value) {
                $ir->getHeader()->setField($key, $value[0]);
            }

            if (self::isApplicationJson($response)) {
                $ir->setMessage(json_decode($response->getBody()));
                return $ir;
            }
            elseif (self::isTextHtml($response)) {
                if ($len = $response->getBody()->getSize()) {
                    $ir->setMessage($response->getBody()->read($len));
                }
                return $ir;
            }
            elseif (self::isTextXML($response)) {
                if ($len = $response->getBody()->getSize()) {
                    $ir->setMessage((string) $response->getBody());
                }
                return $ir;
            }
            elseif (self::isTextPlain($response)) {
                if ($len = $response->getBody()->getSize()) {
                    $ir->setMessage($response->getBody()->read($len));
                }
                return $ir;
            }
            else {
                log_message("error", "GuzzleWrapper error - invalid content-type");
                return false;
            }
        }
        catch (\Exception $e) {
            log_message("error", "GuzzleWrapper error - " . $e->getMessage());
            return false;
        }
    }

    public static function isApplicationJson(Response $response)
    {
        return self::hasHeader($response, 'Content-Type', 'application/json');
    }

    public static function isTextHtml(Response $response)
    {
        return self::hasHeader($response, 'Content-Type', 'text/html');
    }

    public static function isTextXML(Response $response)
    {
        return self::hasHeader($response, 'Content-Type', 'text/xml');
    }

    public static function isTextPlain(Response $response)
    {
        return self::hasHeader($response, 'Content-Type', 'text/plain');
    }

    public static function hasHeader(Response $response, $key, $value)
    {
        if ($headerline = $response->getHeaderLine($key)) {
            if (strpos($headerline, $value) !== false)
                return true;
        }

        return false;
    }

    public static function exception($e)
    {
        error_log('error', 'HTTP Service - ' . $e->getMessage());
        return false;
    }
}
