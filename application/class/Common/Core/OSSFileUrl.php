<?php

namespace Common\Core;

use Common\Helper\FileUploader\S3UploaderInterface;
use Common\Helper\FileUploader\FileUploader;

class OSSFileUrl implements \JsonSerializable {
    
    protected $fileName;
    protected $fileURL;

    /**
     * @return mixed
     */
    public function getFileName() {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName): void {
        $this->fileName = $fileName;
    }

    /**
     * @return mixed
     */
    public function getFileURL() {
        return $this->fileURL;
    }

    /**
     * @param mixed $fileURL
     */
    public function setFileURL($fileURL): void {
        $this->fileURL = $fileURL;
    }

    public function jsonSerialize() {
        return [
            'name' => $this->getFileName(),
            'url' => $this->getFileURL()
        ];
    }
}
