<?php

namespace Common\Helper;

use Common\Core\EncryptedField;
use Common\Core\OSSFileUrl;
use Common\Helper\FileUploader\OSSUploaderInterface;
use Common\Helper\FileUploader\FileUploader;

class EncryptedOSSUrl extends OSSFileUrl
{

    function __construct(FieldEncryptionInterface $encryptor)
    {
        parent::__construct();

        $this->fileURL = new EncryptedField($encryptor);
    }

    public function setUrl($url)
    {
        $this->fileURL->setValue($url);
        return $this;
    }

    public function getUrl()
    {
        return $this->fileURL->getValue();
    }

    public function getUrlEncryptedField()
    {
        return $this->fileURL;
    }
}
