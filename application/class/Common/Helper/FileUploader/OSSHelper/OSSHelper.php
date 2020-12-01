<?php

namespace Common\Helper\FileUploader\OSSHelper;

use OSS\OssClient;
use OSS\Core\OssException;

class OSSHelper implements OSSHelperInterface
{

    protected $config;

    //default
    protected $filepath;
    protected $filename;
    protected $bucket;
    protected $ossClient;
    protected $acl;
    protected $valid_period = '3 minutes';
    protected $endpoint;
    protected $domain;

    public function __construct($config = NULL)
    {
        if ($config == NULL)
            $config = $this->getDefaultConfig();

        $this->setConfig($config);
        $this->connect();
    }
    
    /**
     * 
     * @param array $config('bucket','accessKeyId','accessKeySecret','endpoint')
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        if(isset($config['endpoint'])){
            $this->endpoint = $config['endpoint'];
        }
        if(isset($config['domain'])){
            $this->domain = $config['domain'];
        }
    }

    /**
     * 
     * @return type
     */
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
        $domain = getenv("FILE_CLOUD_DOMAIN");
        
        return array('bucket' => $ossBucket, 'accessKeyId' => $accessKeyId, 'accessKeySecret' => $accessKeySecret, 'endpoint' => $endpoint, 'domain' => $domain);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function connect()
    {
        $accessKeyId = $this->config['key'];
        $accessKeySecret = $this->config['secret'];
        $endpoint = $this->config['endpoint'];
        
        $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
    }

    public function getClient()
    {
        return $this->ossClient;
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
            $bucket = $this->getBucket();
            $object = $key;
            
            //if($this->getClient()->doesObjectExist($bucket, $object)){
                if($this->getAcl() == OSSAcl::PUBLIC_READ || $this->getAcl() == OSSAcl::PUBLIC_READ_WRITE){
                    if(strpos($this->domain,'http://') >= 0 || strpos($this->domain,'https://') >= 0){
                        $url = $this->domain . '/' . $key;
                    }
                    else{
                        $url = 'http://' . $this->domain . '/' . $key;
                    }
                }
                else{
                    $url = $this->getClient()->signUrl($bucket, $object);
                }
                return $url;
            //}
            error_log('OSS Uploader Error - file not exists');
            return FALSE;
        } catch (OssException  $e) {
            error_log('OSS Uploader Error - ' . $e->getMessage());
            return FALSE;
        } catch (\Exception $e) {
            error_log('OSS Uploader Error - ' . $e->getMessage());
            return FALSE;
        }
    }
    
    public function createObject($src_path, $key, $acl = OSSAcl::PRIVATE_ACCESS)
    {
        if($acl == null){
            $acl = $this->getAcl();
            if($acl == null){
                $acl = OSSAcl::PRIVATE_ACCESS;
            }
        }
        $this->setAcl($acl);
        
        try {
            
            $bucket = $this->getBucket();
            $object = $key;
            $file = $src_path;
            $options = array(OssClient::OSS_HEADERS => array(OssClient::OSS_OBJECT_ACL => $acl));
            
            if(strpos($key, '/') > 0){
                $path = substr($key, 0, strrpos($key, '/'));
                if(!$this->getClient()->createObjectDir($bucket, $path)){
                    error_log('OSS Uploader Error - createObjectDir fail');
                    return false;
                }
            }
            
            if ($response = $this->getClient()->uploadFile($bucket, $object, $file, $options)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (OssException  $e) {
            error_log('OSS Uploader Error - ' . $e->getMessage());
            return FALSE;
        } catch (\Exception $e) {
            error_log('OSS Uploader Error - ' . $e->getMessage());
            return FALSE;
        }
    }

    public function copyObject($src_key, $key, $asPublic = false)
    {
        try {
            
            if ($asPublic)
                $acl = OSSAcl::PUBLIC_READ;
            else
                $acl = OSSAcl::PRIVATE_ACCESS;

            $fromBucket = $this->getBucket();
            $fromObject = $src_key;
            $toBucket = $this->getBucket();
            $toObject = $key;
            $options = array(OssClient::OSS_HEADERS => array(OssClient::OSS_OBJECT_ACL => $acl));
            
            if ($this->getClient()->copyObject($fromBucket, $fromObject, $toBucket, $toObject, $options))
                return true;
            else
                return false;
        } catch (OssException $e) {
            error_log('OSS Uploader Access Error - ' . $e->getMessage());
            return FALSE;
        } catch (\Exception $e) {
            error_log('OSS Uploader Error - ' . $e->getMessage());
            return FALSE;
        } 
    }
}
