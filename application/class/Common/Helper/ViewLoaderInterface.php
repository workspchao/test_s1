<?php

namespace Common\Helper;

interface ViewLoaderInterface
{
    public function load($viewFileName, $param);
}
