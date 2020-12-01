<?php

namespace Common\Helper\FileUploader;

class BaseFileUploader
{

    const ALLOWED_TYPE =  array('jpg','png','gif','jpeg');

    protected $file_name;
    //default
    protected $overwrite = TRUE;
    protected $upload_path = './upload/';
    protected $allowed_type;
    protected $max_upload_size = 100000;    //100 kb

    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
        return true;
    }

    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
        return true;
    }

    public function setUploadPath($upload_path)
    {
        $this->upload_path = $upload_path;
        return true;
    }

    /*
     * format: e.g. "jpg|png|bmp"
     * This function will convert to upper case
     */
    public function setAllowedType($allowed_type)
    {
        $this->allowed_type = explode('|', $allowed_type);
        foreach ($this->allowed_type as &$value)
            $value = strtoupper($value);
        return true;
    }

    public function setMaxUploadSize($max_size)
    {
        $this->max_upload_size = $max_size;
        return true;
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    public function getOverwrite()
    {
        return $this->overwrite;
    }

    public function getUploadPath()
    {
        return $this->upload_path;
    }

    public function getAllowedType()
    {
        if ($this->allowed_type)
            return $this->allowed_type;
        else
            return FileUploader::ALLOWED_TYPE;
    }

    public function getMaxUploadSize()
    {
        return $this->max_upload_size;
    }

    /*
     * process the upload, from post data to destination folder
     */
    public function upload($param_name)
    {
        if (!$this->_checkFileExists($param_name))
            return false;

        if (!$this->_checkFileSize($param_name))
            return false;

        if (!$this->_checkFileType($param_name))
            return false;

        if (!$this->_changeFileType($param_name))
            return false;

        if(!file_exists($this->upload_path)){
            if(mkdir($this->upload_path, 0777, true)){
                error_log('Upload file Error - mkdir fail');
                return false;
            }
        }
        
        $target_filename = $this->getUploadPath() . $this->getFileName();
        return move_uploaded_file($_FILES[$param_name]['tmp_name'], $target_filename);
    }

    public function removeFile()
    {
        unlink($this->getUploadPath() . $this->getFileName());
    }

    protected function _checkFileExists($param_name)
    {
        //if to be overwritten, ignore this check
        if ($this->getOverwrite())
            return true;

        if ($file = $this->_getUploadedFile($param_name)) {
            return !file_exists($file['tmp_name']);
        }

        return false;
    }

    protected function _checkFileType($param_name)
    {
        if ($file = $this->_getUploadedFile($param_name)) {
            $filetype = $this->_getFileType($file['name']);
            if (in_array(strtoupper($filetype), $this->getAllowedType()))
                return true;
        }
        return false;
    }

    protected function _checkFileSize($param_name)
    {
        if ($file = $this->_getUploadedFile($param_name)) {
            return ($file['size'] <= $this->getMaxUploadSize());
        }

        return false;
    }

    protected function _getFileType($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    private function _getUploadedFile($param_name)
    {
        if (is_uploaded_file($_FILES[$param_name]['tmp_name']))
            return $_FILES[$param_name];

        return false;
    }

    /*
     * This function to make sure source and dest file are same type
     */
    private function _changeFileType($param_name)
    {
        if ($file = $this->_getUploadedFile($param_name)) {
            $src_filetype = $this->_getFileType($file['name']);

            if ($dest_filetype = $this->_getFileType($this->getFileName())) {
                if ($dest_filetype != $src_filetype) {
                    $newFileName = substr($this->getFileName(), 0, strlen($this->getFileName()) - strlen($dest_filetype)) . $src_filetype;
                    $this->setFileName($newFileName);
                }

                return true;
            } else { //no file type
                $newFileName = $this->getFileName() . "." . $src_filetype;
                $this->setFileName($newFileName);

                return true;
            }
        }

        return false;
    }
}
