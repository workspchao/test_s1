<?php

namespace Common\Core;

use Common\Helper\FileUploader\S3UploaderInterface;
use Common\Helper\FileUploader\FileUploader;

class S3FileUrl implements \JsonSerializable
{

    protected $url;
    protected $url_s3;

    protected $s3;

    function __construct(S3UploaderInterface $s3 = NULL)
    {
        if ($s3 != NULL) {
            $this->s3 = $s3;    //no cloning to save memory
            $this->url_s3 = $this->s3->getUrl();
        }
    }

    public function setUrl($url, S3UploaderInterface $s3 = NULL)
    {
        $this->url = $url;
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
        return $this->url;
    }

    public function getS3Url()
    {
        return $this->url_s3;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getUrl(),
            'url'  => $this->getS3Url()
        ];
    }
}
