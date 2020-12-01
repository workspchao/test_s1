<?php

namespace AccountService\VersionControl;

use AccountService\Common\MessageCode;
use Common\Helper\GuidGenerator;
use Common\Core\BaseService;
use Common\Core\BaseDateTime;
use AccountService\Account\SystemAccountService;

class VersionControlService extends BaseService {

    protected static $_instance = NULL;

    public static function build() {
        if (self::$_instance == NULL) {
            $_ci = &get_instance();
            $_ci->load->model('versioncontrol/Version_control_model');
            self::$_instance = new VersionControlService($_ci->Version_control_model);
        }
        return self::$_instance;
    }

    public function addVersionControl(VersionControl $entity) {

        $entity->setCreatedBy($this->getUpdatedBy());
        $entity->setCreatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->insert($entity)){
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_ADD_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_ADD_FAIL);
        return false;
    }

    public function deleteVersionControl($id, $isLogic = true) {

        $filter = new VersionControl();
        $filter->setId($id);

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_NOT_FOUND);
            return false;
        }

        $oldEntity->setDeletedBy($this->getUpdatedBy());
        $oldEntity->setDeletedAt(BaseDateTime::now());

        if($this->getRepository()->delete($oldEntity, $isLogic)){
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_DELETE_SUCCESS);
            return true;
        }

        $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_DELETE_FAIL);
        return false;
    }

    public function updateVersionControl(VersionControl $entity) {

        $filter = new VersionControl();
        $filter->setId($entity->getId());

        $oldEntity = null;
        if($collection = $this->getRepository()->select($filter)){
            $oldEntity = $collection->result->current();
        }

        if(!$oldEntity){
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_NOT_FOUND);
            return false;
        }

        $entity->setUpdatedBy($this->getUpdatedBy());
        $entity->setUpdatedAt(BaseDateTime::now());

        if($entity = $this->getRepository()->update($entity)){
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_UPDATE_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_UPDATE_FAIL);
        return false;
    }

    public function selectVersionControl(VersionControl $filter, $orderBy = NULL, $limit = NULL, $page = NULL) {

        if($collection = $this->getRepository()->select($filter, $orderBy, $limit, $page)){
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_GET_SUCCESS);
            return $collection;
        }

        $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_NOT_FOUND);
        return false;
    }

    public function getVersionControl($id) {

        if($entity = $this->getRepository()->getById($id)){
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_GET_SUCCESS);
            return $entity;
        }

        $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_NOT_FOUND);
        return false;
    }

    public function getVersionControlByAppId($app_id) {
        
        if(!$app_id){
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_NOT_FOUND);
            return false;
        }
        
        $filterVersionControl = new VersionControl();
        $filterVersionControl->setAppId($app_id);

        if ($collection = $this->selectVersionControl($filterVersionControl)) {
            $entityVersionControl = $collection->result->current();
            
            $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_GET_SUCCESS);
            return $entityVersionControl;
        }

        $this->setResponseCode(MessageCode::CODE_VERSION_CONTROL_NOT_FOUND);
        return false;
    }

    public function authorizeClient($app_id, $version = NULL, $platform = NULL){
        
        $filterVersionControl = new VersionControl();
        $filterVersionControl->setAppId($app_id);

        if ($collection = $this->selectVersionControl($filterVersionControl)) {
            $entityVersionControl = $collection->result->current();
            
            $token = NULL;
            if ($sysUserId = $entityVersionControl->getSystemUserId()) {
                if (!$token = $this->_systemLogin($sysUserId)) {
                    log_message("error", "authorizeClient - systemLogin failed - token not found.");
                    $this->setResponseCode(MessageCode::CODE_CLIENT_NOT_AUTHORISED);
                    return array(false, null);
                }
            }
            
            if(empty($version) && empty($platform)){
                $this->setResponseCode(MessageCode::CODE_CLIENT_AUTHORISED);
                return array(true, array('token' => $token));
            }
            
//            //check if given version 
//            if(!empty($version)){
//                //check if given version is same as latest version
//                $refVersion = $entityVersionControl->getVersion();
//                if (!$this->_checkVersion($version, $refVersion)) {
//                    log_message("error", "authorizeClient - version outdated - need update client.");
//                    //update required
//                    $this->setResponseCode(MessageCode::CODE_OUTDATED_VERSION);
//                    return array(false, $entityVersionControl->getSelectedField(array('version', 'download_url')));
//                }
//            }
            
            //check platform
            if(!empty($platform)){
                if($platform != $entityVersionControl->getPlatform()){
                    log_message("error", "authorizeClient - platform not match. $platform," . json_encode($entityVersionControl));
                    $this->setResponseCode(MessageCode::CODE_CLIENT_NOT_AUTHORISED);
                    return array(false, null);
                }
            }

            $this->setResponseCode(MessageCode::CODE_VERSION_OK);
            return array(true, array('token' => $token));
        }

        $this->setResponseCode(MessageCode::CODE_CLIENT_NOT_AUTHORISED);
        return array(false, null);
    }
    
    protected function _systemLogin($system_user_id) {
        $sysUserServ = SystemAccountService::build();
        $sysUserServ->setIpAddress($this->getIpAddress());
        $sysUserServ->setUpdatedBy($system_user_id);
        if (list($login_account, $token) = $sysUserServ->systemLogin($system_user_id)) {
            return $token->getToken();
        }

        return false;
    }
    
    protected function _checkVersion($version, $ref_version)
    {
        $ref_version_numbers = explode(".", $ref_version);
        $version_numbers = explode(".", $version);

        if( count($ref_version_numbers) <> count($version_numbers) )
            return false;

        $checking = 0;
        foreach( $ref_version_numbers AS $ref_number )
        {
            //checking major, the first two numbers is major
            if($checking <= 1)
            {
                if( !is_numeric($ref_number) or !is_numeric($version_numbers[$checking]) )
                    return false;

                if( $version_numbers[$checking] < $ref_number )
                    return false;
            }

            $checking = $checking + 1;
        }

        return true;
    }

}
