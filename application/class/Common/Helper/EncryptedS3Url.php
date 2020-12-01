<?php

namespace Common\Helper;

use Common\Core\EncryptedField;
use Common\Core\S3FileUrl;
use Common\Helper\FileUploader\S3UploaderInterface;
use Common\Helper\FileUploader\FileUploader;

class EncryptedS3Url extends S3FileUrl
{

    function __construct(S3UploaderInterface $s3 = NULL, FieldEncryptionInterface $encryptor)
    {
        parent::__construct($s3);

        $this->url = new EncryptedField($encryptor);
    }

    public function setUrl($url, S3UploaderInterface $s3 = NULL)
    {
        $this->url->setValue($url, $s3);
        if ($s3 instanceof FileUploader) {
            $s3->setFileName($url);
            $this->url_s3 = $s3->getUrl();
        } elseif ($this->s3 instanceof FileUploader) {
            $this->s3->setFileName($url);
            $this->url_s3 = $this->s3->getUrl();
        }
        return $this;
    }

    public function getUrl()
    {
        return $this->url->getValue();
    }

    public function getUrlEncryptedField()
    {
        return $this->url;
    }
}
