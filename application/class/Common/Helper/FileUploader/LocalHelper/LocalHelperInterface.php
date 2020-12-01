<?php

namespace Common\Helper\FileUploader\LocalHelper;

interface LocalHelperInterface
{

    //for ip, username, password etc...
    public function setConfig(array $config);
    public function getConfig();

    //construct the local client
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
     * get local url
     */
    public function getFileUrl($key);

    /*
     * save file into local
     */
    public function createObject($src_path, $key);
    public function copyObject($src_key, $key, $asPublic = false);
}
