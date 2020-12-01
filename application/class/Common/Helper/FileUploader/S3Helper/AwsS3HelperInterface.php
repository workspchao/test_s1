<?php

namespace Common\Helper\FileUploader\S3Helper;

interface AwsS3HelperInterface
{

    //for ip, username, password etc...
    public function setConfig(array $config);
    public function getConfig();

    //construct the s3 client
    public function connect();

    //getters/setters
    public function getClient();
    public function setBucket($bucket_name);
    public function setValidPeriod($period);
    public function setFilePath($filepath);
    public function getBucket();
    public function getValidPeriod();
    public function getFilePath();

    /*
     * get s3 url
     */
    public function getFileUrl($key);

    /*
     * save file into s3
     */
    public function createObject($src_path, $key);
    public function copyObject($src_key, $key, $asPublic = false);
}
