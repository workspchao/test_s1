<?php

namespace Common\Helper\FileUploader\LocalHelper;

class LocalHelperFactory
{
    public static function build()
    {
        $localObj = new LocalHelper();
        return $localObj;
    }
}
