<?php

namespace Common\Helper\FileUploader\LocalHelper;

class LocalHelper implements LocalHelperInterface
{

    protected $config;

    protected $basepath = './upload/';
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

    public function getDefaultConfig()
    {
        $endpoint = getenv("FILE_CLOUD_ENDPOINT");
        $domain = getenv("FILE_CLOUD_DOMAIN");
        
        return array('endpoint' => $endpoint, 'domain' => $domain);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function connect()
    {
        return true;
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
            if(strpos($this->domain,'http://') >= 0 || strpos($this->domain,'https://') >= 0){
                $url = str_replace('./', '/', $this->basepath) . '/' . $key;
                $url = str_replace("//", "/", $url);
                $url = $this->domain . $url;
            }
            else{
                $url = 'http://' . $this->domain . str_replace('./', '/', $this->basepath) . '/' . $key;
            }
            return $url;
        } catch (\Exception $e) {
            error_log('Local Uploader Error - ' . $e->getMessage());
            return FALSE;
        }
    }
    
    public function createObject($src_path, $key)
    {
        $target_path = $this->basepath . $key;
        try {
            
            if(strpos($target_path, '/') > 0){
                $path = substr($target_path, 0, strrpos($target_path, '/'));
                if(!file_exists($path)){
                    if(!mkdir($path, 0777, true)){
                        error_log('Local Uploader Error - mkdir fail');
                        return false;
                    }
                }
            }
            
            return rename($src_path, $target_path);
        } catch (\Exception $e) {
            error_log('Local Uploader Error - ' . $e->getMessage());
            return FALSE;
        }
    }

    public function copyObject($src_key, $key, $asPublic = false)
    {
        $target_path = $this->basepath . $key;
        try {
            return copy($src_path, $target_path);
        } catch (\Exception $e) {
            error_log('Local Uploader Error - ' . $e->getMessage());
            return FALSE;
        } 
    }
}
