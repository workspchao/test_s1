<?php

namespace Common\Helper\FileUploader;

class BaseImageUploader extends BaseFileUploader
{

    protected $basePath = './upload/image/';
    /*
     * For Images
     */
    protected $smallDefaultSize = 200;
    protected $mediumDefaultSize = 400;
    protected $largeDefaultSize = 800;
    protected $maxImageSize = 8000;

    function __construct()
    {
        $this->setOverwrite(true);
        $this->setUploadPath($this->basePath);
        $this->setAllowedType('jpg|png|gif|jpeg');
    }

    public function setUploadPath($path)
    {
        $this->setBasePath($path);
        parent::setUploadPath($this->_getOriginalPath());
    }

    public function setBasePath($path)
    {
        $this->basePath = $path;
    }

    public function setSmallSize($size)
    {
        $this->smallDefaultSize = $size;
        return true;
    }

    public function setMediumSize($size)
    {
        $this->mediumDefaultSize = $size;
        return true;
    }

    public function setLargeSize($size)
    {
        $this->largeDefaultSize = $size;
        return true;
    }

    public function setMaxSize($size)
    {
        $this->maxImageSize = $size;
        return true;
    }

    public function getSmallSize()
    {
        return $this->smallDefaultSize;
    }

    public function getMediumSize()
    {
        return $this->mediumDefaultSize;
    }

    public function getLargeSize()
    {
        return $this->largeDefaultSize;
    }

    public function getMaxSize()
    {
        return $this->maxImageSize;
    }

    /*
     * this function will upload the imageFile to server, resize and save in respective folders
     */
    public function upload($param_name)
    {
        if (parent::upload($param_name)) {
            //resize and save in respective image
            $this->_resizetoSmall();
            $this->_resizetoMedium();
            $this->_resizetoLarge();

            return true;
        }

        return false;
    }

    public function removeImages()
    {
        if (file_exists($this->_getOriginalPath() . $this->getFileName()))
            unlink($this->_getOriginalPath() . $this->getFileName());

        if (file_exists($this->_getSmallPath() . $this->getFileName()))
            unlink($this->_getSmallPath() . $this->getFileName());

        if (file_exists($this->_getMediumPath() . $this->getFileName()))
            unlink($this->_getMediumPath() . $this->getFileName());

        if (file_exists($this->_getLargePath() . $this->getFileName()))
            unlink($this->_getLargePath() . $this->getFileName());
    }

    protected function _resizetoSmall()
    {
        return $this->_resizeImage(
            $this->getUploadPath() . $this->getFileName(),
            $this->getSmallSize(),
            $this->getSmallSize(),
            $this->_getSmallPath() . $this->getFileName()
        );
    }

    protected function _resizetoMedium()
    {
        return $this->_resizeImage(
            $this->getUploadPath() . $this->getFileName(),
            $this->getMediumSize(),
            $this->getMediumSize(),
            $this->_getMediumPath() . $this->getFileName()
        );
    }

    protected function _resizetoLarge()
    {
        return $this->_resizeImage(
            $this->getUploadPath() . $this->getFileName(),
            $this->getLargeSize(),
            $this->getLargeSize(),
            $this->_getLargePath() . $this->getFileName()
        );
    }

    protected function _getSmallPath()
    {
        return $this->basePath . 's/';
    }

    protected function _getMediumPath()
    {
        return $this->basePath . 'm/';
    }

    protected function _getLargePath()
    {
        return $this->basePath . 'l/';
    }

    protected function _getOriginalPath()
    {
        return $this->basePath . 'o/';
    }

    protected function _resizeImage($file, $w, $h, $dest_folder)
    {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;

        if ($w / $h > $r) {
            $newwidth = $h * $r;
            $newheight = $h;
        } else {
            $newheight = $w / $r;
            $newwidth = $w;
        }

        $imageFileType = $this->_getFileType($file);
        $src = $this->_createImageByType($imageFileType, $file);
        $img = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($img, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $this->_outputImageByType($imageFileType, $img, $dest_folder);
    }

    protected function _createImageByType($imageType, $file)
    {
        switch (strtoupper($imageType)) {
            case "JPG":
            case "JPEG":
                return imagecreatefromjpeg($file);
                break;
            case "GIF":
                return imagecreatefromgif($file);
                break;
            case "PNG":
                return imagecreatefrompng($file);
                break;
        }

        return false;
    }

    protected function _outputImageByType($imageType, $image, $filename)
    {
        switch (strtoupper($imageType)) {
            case "JPG":
            case "JPEG":
                return imagejpeg($image, $filename);
                break;
            case "GIF":
                return imagegif($image, $filename);
                break;
            case "PNG":
                return imagepng($image, $filename);
                break;
        }

        return false;
    }
}
