<?php

namespace Common\Helper\FileUploader\OSSHelper;

class OSSHelperFactory
{

    public static function build($region = OSSRegion::HUADONG1_HANGZHOU, $endpoint = NULL, $bucket = NULL, $key = NULL, $secret = NULL, $domain = NULL)
    {
        $config = OSSHelperFactory::_getConfig($region, $endpoint, $key, $secret, $domain);

        $awsS3Obj = new OSSHelper($config);
        $awsS3Obj->setBucket(OSSHelperFactory::_getBucket($bucket));
        return $awsS3Obj;
    }

    private static function _getConfig($region, $endpoint, $key = NULL, $secret = NULL, $domain = NULL)
    {
        $config = array();

        $config['region'] = $region;
        $config['endpoint'] = $endpoint;
        
        if ($key != NULL)
            $config['key'] = $key;

        if ($secret != NULL)
            $config['secret'] = $secret;
        
        if ($domain != NULL)
            $config['domain'] = $domain;

        return $config;
    }

    /*
     * Get the given config, otherwise get from environment variable
     */
    private static function _getBucket($bucket = NULL)
    {
        if ($bucket != NULL)
            return $bucket;

        if (getenv('OSS_BUCKET'))
            return getenv('OSS_BUCKET');

        return NULL;
    }
}
