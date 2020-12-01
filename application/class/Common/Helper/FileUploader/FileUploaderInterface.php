<?php

namespace Common\Helper\FileUploader;

interface FileUploaderInterface
{
    //getters/setters
    public function setClient($client);
    public function getClient();
    public function setCloudFolder($folder);
    public function getCloudFolder();

    public function uploadtoCloud($paramName);
    public function getUrl();
}
