<?php

namespace Common\Helper\FileUploader;

//same as S3 file uploader but file from local path
class LocalS3FileUploader extends S3FileUploader
{

    public function upload($param_name)
    { //no need to upload to local file
        return true;
    }

    public function uploadtoS3($paramName)
    {
        if ($this->s3->createObject(
            $this->getUploadPath() . $this->getFileName(),
            $this->getS3Folder() . $this->getFileName()
        )) {
            unlink($this->getUploadPath() . $this->getFileName());
            return true;
        }

        return false;
    }
}
