<?php

namespace Common\Helper\FileUploader;

use Common\Helper\FileUploader\BaseImageUploader;
use Common\Helper\FileUploader\FileUploaderInterface;
use Common\Helper\FileUploader\FileCloudUploaderFactory;

class ImageCloudUploader extends BaseImageUploader implements FileUploaderInterface
{

    protected $client;
    protected $cloudFolder;
    protected $cloudSourceFolder;

    function __construct($client = NULL)
    {
        parent::__construct();

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
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setCloudFolder($folder)
    {
        $this->cloudFolder = $folder;
        return false;
    }

    public function getCloudFolder()
    {
        return $this->cloudFolder;
    }

    public function setCloudSourceFolder($folder)
    {
        $this->cloudSourceFolder = $folder;
        return false;
    }

    public function getCloudSourceFolder()
    {
        return $this->cloudSourceFolder;
    }

    public function setFileAcl($fileAcl){
        $this->getClient()->setAcl($fileAcl);
        return $this;
    }

    public function uploadtoCloud($paramName, $acl = null)
    {
        if (parent::upload($paramName)) {
            if (!$this->_uploadOriginaltoCloud($acl))
                return false;

            if (!$this->_uploadSmalltoCloud($acl))
                return false;

            if (!$this->_uploadMediumtoCloud($acl))
                return false;

            if (!$this->_uploadLargetoCloud($acl))
                return false;

            $this->removeImages();  //remove from local server
            return true;
        }

        return false;
    }

    public function moveFromCloudFolder()
    {
        if (!$this->_moveOriginalFromCloud())
            return false;

        if (!$this->_moveSmallFromCloud())
            return false;

        if (!$this->_moveMediumFromCloud())
            return false;

        if (!$this->_moveLargeFromCloud())
            return false;

        return true;
    }

    public function getUrl()
    {
        return array(
            'o' => $this->getClient()->getFileUrl($this->_getOriginalKey()),
            's' => $this->getClient()->getFileUrl($this->_getSmallKey()),
            'm' => $this->getClient()->getFileUrl($this->_getMediumKey()),
            'l' => $this->getClient()->getFileUrl($this->_getLargeKey()),
        );
    }

    protected function _uploadOriginaltoCloud($acl)
    {
        return $this->getClient()->createObject(
            $this->_getOriginalPath() . $this->getFileName(),
            $this->_getOriginalKey(),
            $acl
        );
    }

    protected function _uploadSmalltoCloud($acl)
    {
        return $this->getClient()->createObject(
            $this->_getSmallPath() . $this->getFileName(),
            $this->_getSmallKey(),
            $acl
        );
    }

    protected function _uploadMediumtoCloud($acl)
    {
        return $this->getClient()->createObject(
            $this->_getMediumPath() . $this->getFileName(),
            $this->_getMediumKey(),
            $acl
        );
    }

    protected function _uploadLargetoCloud($acl)
    {
        return $this->getClient()->createObject(
            $this->_getLargePath() . $this->getFileName(),
            $this->_getLargeKey(),
            $acl
        );
    }

    protected function _moveOriginalFromCloud()
    {
        return $this->getClient()->copyObject(
            $this->getCloudSourceFolder() . "o/" . $this->getFileName(),
            $this->_getOriginalKey()
        );
    }

    protected function _moveSmallFromCloud()
    {
        return $this->getClient()->copyObject(
            $this->getCloudSourceFolder() . "s/" . $this->getFileName(),
            $this->_getSmallKey()
        );
    }

    protected function _moveMediumFromCloud()
    {
        return $this->getClient()->copyObject(
            $this->getCloudSourceFolder() . "m/" . $this->getFileName(),
            $this->_getMediumKey()
        );
    }

    protected function _moveLargeFromCloud()
    {
        return $this->getClient()->copyObject(
            $this->getCloudSourceFolder() . "l/" . $this->getFileName(),
            $this->_getLargeKey()
        );
    }

    protected function _getOriginalKey()
    {
        return $this->getCloudFolder() . "o/" . $this->getFileName();
    }

    protected function _getSmallKey()
    {
        return $this->getCloudFolder() . "s/" . $this->getFileName();
    }

    protected function _getMediumKey()
    {
        return $this->getCloudFolder() . "m/" . $this->getFileName();
    }

    protected function _getLargeKey()
    {
        return $this->getCloudFolder() . "l/" . $this->getFileName();
    }
}
