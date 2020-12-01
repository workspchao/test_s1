<?php

namespace Common\Helper\FileUploader\S3Helper;

use Aws\S3\S3Client;

class AwsS3Helper implements AwsS3HelperInterface
{

    protected $config;

    //default
    protected $filepath;
    protected $filename;
    protected $bucket;
    protected $s3client;
    protected $acl;
    protected $valid_period = '3 minutes';

    public function __construct($config = NULL)
    {
        if ($config == NULL)
            $config = $this->getDefaultConfig();

        $this->setConfig($config);
        $this->connect();
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function getDefaultConfig()
    {
        //FILE_CLOUD_MODE=OSS
        //FILE_CLOUD_ENDPOINT=
        //FILE_CLOUD_REGION=
        //FILE_CLOUD_BUCKET=
        //FILE_CLOUD_KEY=
        //FILE_CLOUD_SECRET=
        
        $ossBucket = getenv("FILE_CLOUD_BUCKET");
        if(empty($ossBucket)){
            $ossBucket = 'default';
        }
        $accessKeyId = getenv("FILE_CLOUD_KEY");
        $accessKeySecret = getenv("FILE_CLOUD_SECRET");
        $endpoint = getenv("FILE_CLOUD_ENDPOINT");
        
        return array('region' => AwsS3Region::SINGAPORE);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function connect()
    {
        $this->s3client = S3Client::factory($this->getConfig());
    }

    public function getClient()
    {
        return $this->s3client;
    }

    public function setBucket($bucket_name)
    {
        $this->bucket = $bucket_name;
    }

    public function setValidPeriod($period)
    {
        $this->valid_period = $period;
    }

    public function setFilePath($filepath)
    {
        $this->filepath = $filepath;
    }

    public function setAcl($acl)
    {
        $this->acl = $acl;
    }

    public function getBucket()
    {
        return $this->bucket;
    }

    public function getValidPeriod()
    {
        return $this->valid_period;
    }

    public function getFilePath()
    {
        return $this->filepath;
    }

    public function getAcl()
    {
        return $this->acl;
    }

    public function getFileUrl($key)
    {
        try {
            if ($this->getAcl() == AwsS3CannedAcl::PUBLIC_READ) {

                $url = $this->getClient()->getObjectUrl(
                    $this->getBucket(),
                    $key
                );

                return $url;
            } else {

                $command = $this->getClient()->getCommand('GetObject', [
                    'Bucket' => $this->getBucket(),
                    'Key'    => $key
                ]);

                $request = $this->getClient()->createPresignedRequest($command, '+20 minutes');

                return  (string) $request->getUri();
            }
        } catch (Aws\S3\Exception\S3Exception $e) {
            error_log('S3 Uploader Error - ' . $e->getMessage());
            return FALSE;
        }
    }

    public function createObject($src_path, $key, $acl = AwsS3CannedAcl::PRIVATE_ACCESS)
    {
        try {
            if($acl == null){
                $acl = $this->getAcl();
                if($acl == null){
                    $acl = AwsS3CannedAcl::PRIVATE_ACCESS;
                }
            }
            $this->setAcl($acl);

            if ($this->getClient()->putObject(array(
                'Bucket'               => $this->getBucket(),
                'Key'                  => $key,
                'SourceFile'           => $src_path,
                'ACL'                  => $acl
            ))) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (\Aws\S3\Exception\InvalidAccessKeyIdException $e) {
            error_log('S3 Uploader Access Error - ' . $e->getMessage());
            return FALSE;
        } catch (Aws\S3\Exception\S3Exception $e) {
            error_log('S3 Uploader Error - ' . $e->getMessage());
            return FALSE;
        }
    }

    public function copyObject($src_key, $key, $asPublic = false)
    {
        if ($asPublic)
            $acl = AwsS3CannedAcl::PUBLIC_READ;
        else
            $acl = AwsS3CannedAcl::PRIVATE_ACCESS;

        try {
            if ($this->getS3()->copyObject(array(
                'Bucket' => $this->getBucket(),
                'Key' => $key,
                'CopySource' => "{$this->getBucket()}/{$src_key}",
                'ACL' => $acl
            )))
                return true;
            else
                return false;
        } catch (\Aws\S3\Exception\InvalidAccessKeyIdException $e) {
            error_log('S3 Uploader Access Error - ' . $e->getMessage());
            return FALSE;
        } catch (Aws\S3\Exception\S3Exception $e) {
            error_log('S3 Uploader Error - ' . $e->getMessage());
            return FALSE;
        } 
    }
}
