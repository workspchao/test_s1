<?php

namespace Common\Helper\FileUploader\S3Helper;

class AwsS3CannedAcl
{
    const PRIVATE_ACCESS = 'private';
    const PUBLIC_READ = 'public-read';
    const PUBLIC_READ_WRITE = 'public-read-write';
    const AUTHENTICATED_READ = 'authenticated-read';
    const BUCKET_OWNER_READ = 'bucket-owner-read';
    const BUCKET_OWNER_FULL_CONTROL = 'bucket-owner-full-control';
}
