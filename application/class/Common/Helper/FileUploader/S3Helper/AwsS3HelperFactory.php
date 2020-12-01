<?php

namespace Common\Helper\FileUploader\S3Helper;

class AwsS3HelperFactory
{

    public static function build($region = AwsS3Region::SINGAPORE, $bucket = NULL, $key = NULL, $secret = NULL)
    {
        $config = AwsS3HelperFactory::_getConfig($region, $key, $secret);

        $awsS3Obj = new AwsS3Helper($config);
        $awsS3Obj->setBucket(AwsS3HelperFactory::_getBucket($bucket));
        return $awsS3Obj;
    }

    private static function _getConfig($region, $key = NULL, $secret = NULL)
    {
        $config = array();

        //$config['profile'] = 'default'; can not use default, because using IAM user now.
        $config['region'] = $region;
        $config['version'] = '2006-03-01';

        if ($key != NULL)
            $config['key'] = $key;

        if ($secret != NULL)
            $config['secret'] = $secret;

        return $config;
    }

    /*
     * Get the given config, otherwise get from environment variable
     */
    private static function _getBucket($bucket = NULL)
    {
        if ($bucket != NULL)
            return $bucket;

        if (getenv('S3_BUCKET'))
            return getenv('S3_BUCKET');

        return NULL;
    }
}
