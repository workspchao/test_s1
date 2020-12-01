<?php

namespace AccountService\VersionControl;

use Common\Core\BaseEntity;
use Common\Core\BaseDateTime;

class VersionControl extends BaseEntity {

    const TABLE_NAME = 'version_control';

    private $app_id;
    private $app_key;
    private $appname;
    private $version;
    private $platform;
    private $download_url;
    private $system_user_id;
    private $hot_version;
    private $hot_download_url;

    public function setAppId($app_id) {
        $this->app_id = $app_id;
        return $this;
    }

    public function getAppId() {
        return $this->app_id;
    }

    public function setAppKey($app_key) {
        $this->app_key = $app_key;
        return $this;
    }

    public function getAppKey() {
        return $this->app_key;
    }

    public function setAppname($appname) {
        $this->appname = $appname;
        return $this;
    }

    public function getAppname() {
        return $this->appname;
    }

    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setPlatform($platform) {
        $this->platform = $platform;
        return $this;
    }

    public function getPlatform() {
        return $this->platform;
    }

    public function setDownloadUrl($download_url) {
        $this->download_url = $download_url;
        return $this;
    }

    public function getDownloadUrl() {
        return $this->download_url;
    }

    public function setSystemUserId($system_user_id) {
        $this->system_user_id = $system_user_id;
        return $this;
    }

    public function getSystemUserId() {
        return $this->system_user_id;
    }

    public function setHotVersion($hot_version) {
        $this->hot_version = $hot_version;
        return $this;
    }

    public function getHotVersion() {
        return $this->hot_version;
    }
    
    public function setHotDownloadUrl($hot_download_url) {
        $this->hot_download_url = $hot_download_url;
        return $this;
    }

    public function getHotDownloadUrl() {
        return $this->hot_download_url;
    }

    public function jsonSerialize() {

        $json = parent::jsonSerialize();

        $json["app_id"] = $this->getAppId();
        $json["app_key"] = $this->getAppKey();
        $json["appname"] = $this->getAppname();
        $json["version"] = $this->getVersion();
        $json["platform"] = $this->getPlatform();
        $json["download_url"] = $this->getDownloadUrl();
        $json["system_user_id"] = $this->getSystemUserId();
        $json["hot_version"] = $this->getHotVersion();
        $json["hot_download_url"] = $this->getHotDownloadUrl();

        return $json;
    }

}
