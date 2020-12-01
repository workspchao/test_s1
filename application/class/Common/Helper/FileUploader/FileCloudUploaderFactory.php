<?php

namespace Common\Helper\FileUploader;

use Common\Helper\FileUploader\S3Helper\AwsS3Region;
use Common\Helper\FileUploader\S3Helper\AwsS3HelperFactory;
use Common\Helper\FileUploader\OSSHelper\OSSRegion;
use Common\Helper\FileUploader\OSSHelper\OSSHelperFactory;
use Common\Helper\FileUploader\LocalHelper\LocalHelperFactory;

class FileCloudUploaderFactory
{
    protected static $_instance;
    
    public static function build()
    {
        $cloudMode = getenv("FILE_CLOUD_MODE");
        
        $endpoint = self::_getEndPoint();
        $region = self::_getRegion();
        $bucket = self::_getBucket();
        $key = self::_getKey();
        $secret = self::_getSecret();
        $domain = self::_getDomain();
        
        if($cloudMode == FileCloudMode::S3){
            self::$_instance = AwsS3HelperFactory::build($region, $bucket, $key, $secret);
        }
        else if($cloudMode == FileCloudMode::OSS){
            self::$_instance = OSSHelperFactory::build($region, $endpoint, $bucket, $key, $secret, $domain);
        }
        else{
            self::$_instance = LocalHelperFactory::build();
        }

        return self::$_instance;
    }

    /*
     * Get the given config, otherwise get from environment variable
     */
    private static function _getEndPoint($endpoint = NULL)
    {
        if ($endpoint != NULL)
            return $endpoint;

        $endpoint = getenv('FILE_CLOUD_ENDPOINT');
        if($endpoint){
            return $endpoint;
        }
        
        return NULL;
    }

    private static function _getRegion($region = NULL)
    {
        if ($region != NULL)
            return $region;

        $region = getenv('FILE_CLOUD_REGION');
        if($region){
            return $region;
        }
        
        return NULL;
    }
    
    private static function _getBucket($bucket = NULL)
    {
        if ($bucket != NULL)
            return $bucket;

        $bucket = getenv('FILE_CLOUD_BUCKET');
        if($bucket){
            return $bucket;
        }
        
        return NULL;
    }
    
    private static function _getKey($key = NULL){
        if ($key != NULL)
            return $key;

        $key = getenv('FILE_CLOUD_KEY');
        if($key){
            return $key;
        }
        
        return NULL;
    }
    
    private static function _getSecret($secret = NULL){
        if ($secret != NULL)
            return $secret;

        $secret = getenv('FILE_CLOUD_SECRET');
        if($secret){
            return $secret;
        }
        
        return NULL;
    }

    private static function _getDomain($domain = NULL){
        if ($domain != NULL)
            return $domain;

        $domain = getenv('FILE_CLOUD_DOMAIN');
        if($domain){
            return $domain;
        }
        
        return NULL;
    }
    
}
