<?php

namespace Common\Helper\FileUploader;

class FileAcl
{
    /**
     * 私有读写(该 ACL 表明某个 Object 是私有资源，即只有该 Object 的 Owner 拥有该 Object 的读写权限，其他的用户没有权限操作该 Object。)
     */
    const PRIVATE_ACCESS = 'private';
    /**
     * 公共读，私有写(该 ACL 表明某个 Object 是公共读资源，即非 Object Owner 只有该 Object 的读权限，而 Object Owner 拥有该 Object 的读写权限。)
     */
    const PUBLIC_READ = 'public-read';
}
