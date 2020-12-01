<?php

namespace Common\Helper\FileUploader;

use Common\Helper\FileUploader\BaseFileUploader;
use Common\Helper\FileUploader\FileUploaderInterface;
use Common\Helper\FileUploader\FileCloudUploaderFactory;

class FileCloudUploader extends BaseFileUploader implements FileUploaderInterface
{

    protected $client;
    protected $cloudFolder;
    protected $cloudSourceFolder;

    function __construct($client = NULL)
    {
        if ($client == NULL)
            $client = $this->_getDefaultClient();

        $this->setClient($client);
    }

    protected function _getDefaultClient()
    {
        return FileCloudUploaderFactory::build();
    }

    public function setClient($client)
    {
        $this->client = $client;
        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setCloudFolder($folder)
    {
        $this->cloudFolder = $folder;
        return $this;
    }

    public function getCloudFolder()
    {
        return $this->cloudFolder;
    }

    public function setCloudSourceFolder($folder)
    {
        $this->cloudSourceFolder = $folder;
        return $this;
    }

    public function getCloudSourceFolder()
    {
        return $this->cloudSourceFolder;
    }
    
    public function setFileAcl(FileAcl $fileAcl){
        $this->client->setAcl($fileAcl);
        return $this;
    }

    public function uploadtoCloud($paramName, $acl = null)
    {
        if (parent::upload($paramName)) {
            if ($this->client->createObject(
                $this->getUploadPath() . $this->getFileName(),
                $this->getCloudFolder() . $this->getFileName(),
                $acl
            )) {
                unlink($this->getUploadPath() . $this->getFileName());
                return true;
            }
        }

        return false;
    }

    public function getUrl()
    {
        return $this->client->getFileUrl($this->getCloudFolder() . $this->getFileName());
    }
}
