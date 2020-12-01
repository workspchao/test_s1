<?php

use Common\Core\IpAddress;
use AccountService\CoreConfigData\CoreConfigData;
use AccountService\CoreConfigData\CoreConfigDataService;
use AccountService\Fun\FunCode;


class Core_config_data_admin extends Admin_Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        
        $this->_service = CoreConfigDataService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }
    
    // /**
    //  * core config data listing
    //  */
    // public function listing(){
        
    //     $admin_id = $this->_getAdminId(FunCode::SYSTEM_SETTING);
        
    //     $this->_service->setUpdatedBy($admin_id);
        
    //     $limit = $this->_getLimit();
    //     $page = $this->_getPage();
        
    //     $result = array("result" => array(), "total" => 0);
        
    //     $filter = new CoreConfigData();
        
    //     if($collection = $this->_service->selectCoreConfigData($filter, null, $limit, $page)){
    //         $result = $collection;
    //     }

    //     $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
    //     return true;
        
    // }

    // /**
    //  * add core config data
    //  */
    // public function add(){
        
    //     $admin_id = $this->_getAdminId(FunCode::SYSTEM_SETTING);
        
    //     $this->_service->setUpdatedBy($admin_id);
        
    //     $this->required(array('code', 'value'));
        
    //     $code = $this->input_post('code');
    //     $value = $this->input_post('value');
    //     $description = $this->input_post('description');
        
    //     $entity = new CoreConfigData();
    //     $entity->setCode($code);
    //     $entity->setValue($value);
    //     $entity->setDescription($description);
        
    //     if($entity = $this->_service->addCoreConfigData($entity)){
    //         $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $entity));
    //         return true;
    //     }

    //     $this->_respondWithFailedCode($this->_service->getResponseCode(), array('result' => null));
    //     return false;
    // }

    // /**
    //  * edit core config data listing
    //  */
    // public function edit(){
        
    //     $admin_id = $this->_getAdminId(FunCode::SYSTEM_SETTING);
        
    //     $this->_service->setUpdatedBy($admin_id);
        
    //     $this->required(array('id', 'code', 'value'));
        
    //     $id = $this->input_post('id');
    //     $code = $this->input_post('code');
    //     $value = $this->input_post('value');
    //     $description = $this->input_post('description');
        
    //     $entity = new CoreConfigData();
    //     $entity->setId($id);
    //     $entity->setCode($code);
    //     $entity->setValue($value);
    //     $entity->setDescription($description);
        
    //     if($entity = $this->_service->updateCoreConfigData($entity)){
    //         $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $entity));
    //         return true;
    //     }

    //     $this->_respondWithFailedCode($this->_service->getResponseCode(), array('result' => null));
    //     return false;
    // }

    // /**
    //  * get core config data listing
    //  */
    // public function get(){
        
    //     $admin_id = $this->_getAdminId(FunCode::SYSTEM_SETTING);
        
    //     $this->_service->setUpdatedBy($admin_id);
        
    //     $this->required(array('id'));

    //     $id = $this->input_post('id');
        
    //     if($entity = $this->_service->getCoreConfigData($id)){
    //         $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $entity));
    //         return true;
    //     }

    //     $this->_respondWithFailedCode($this->_service->getResponseCode(), array('result' => null));
    //     return true;
    // }

    // /**
    //  * del core config data listing
    //  */
    // public function delete(){
        
    //     $admin_id = $this->_getAdminId(FunCode::SYSTEM_SETTING);
        
    //     $this->_service->setUpdatedBy($admin_id);
        
    //     $this->required(array('id'));

    //     $id = $this->input_post('id');
        
    //     if($result = $this->_service->deleteCoreConfigData($id)){
            
    //         $this->_respondWithSuccessCode($this->_service->getResponseCode(), array('result' => $result));
    //         return true;
    //     }

    //     $this->_respondWithFailedCode($this->_service->getResponseCode(), array('result' => null));
    //     return true;
    // }

}
