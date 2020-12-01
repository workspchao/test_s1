<?php

use Common\Core\IpAddress;
use Common\Helper\InputValidator;
use AccountService\Fun\Fun;
use AccountService\Fun\FunService;
use AccountService\Fun\FunCode;
use AccountService\Fun\FunType;

class Fun_admin extends Admin_Base_Controller {

    protected $_service;

    function __construct() {
        parent::__construct();
        $this->_service = FunService::build();
        $this->_service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    }

    
    // public function getModuleList(){
        
    //     $admin_id = $this->_getAdminId();

    //     $this->_service->setUpdatedBy($admin_id);
        
    //     $filter = new Fun();
    //     $filter->setDisplayType(FunType::MENU);
    //     // $isRoot = true;
    //     if($result = $this->_service->selectFun($filter)){
            
    //         $parent_ids = array();
    //         foreach ($result->result as $entityFun){
    //             $parent_ids[] = $entityFun->getId();
    //         }
    //         $filter = new Fun();
    //         $collectionParent = $this->_service->selectFun($filter);
            
    //         $arrResult = array();
    //         foreach ($result->result as $entityFun) {
    //             $arrFun = $entityFun->getSelectedField(array('id','code','name','display_type','parent_id'));
    //             $arrFun['parent_name'] = null;
    //             $parent_id = $entityFun->getParentId();
    //             if($parent_id){
    //                 if($entityParent = $collectionParent->result->getById($parent_id)){
    //                     $arrFun['parent_name'] = $entityParent->getName();
    //                 }
    //             }
                
    //             $id = $entityFun->getId();
    //             $name = $entityFun->getName();
                
    //             $arrFun['child'] = $this->getChildModuleList($id, $name);
                
    //             $arrResult[] = $arrFun;
    //         }
    //         $result->result = $arrResult;
            
    //         $this->_respondWithSuccessCode($this->_service->getResponseCode(), array("result" => $result));
    //         return true;
    //     }
    //     $this->_respondWithFailedCode($this->_service->getResponseCode());
    //     return false;
    // }
    
    // private function getChildModuleList($parent_id, $parent_name){
        
    //     log_message("debug", "function Fun_admin->getChildModuleList begin");
        
    //     $filter = new Fun();
    //     //$filter->setDisplayType(FunType::MENU);
    //     $filter->setParentId($parent_id);
        
    //     if($result = $this->_service->selectFun($filter)){
    //         $arrResult = array();
    //         foreach ($result->result as $entityFun) {
    //             $arrFun = $entityFun->getSelectedField(array('id','code','name','display_type','parent_id'));
    //             $arrFun['parent_name'] = $parent_name;
    //             $id = $entityFun->getId();
    //             $name = $entityFun->getName();
    //             $arrFun['child'] = $this->getChildModuleList($id, $name);
    //             $arrResult[] = $arrFun;
    //         }
    //         return $arrResult;
    //     }
    //     return null;
    // }
    
    // public function getFunList(){
        
    //     log_message("debug", "function Fun_admin->getFunList begin");
        
    //     $admin_id = $this->_getAdminId();

    //     $this->_service->setUpdatedBy($admin_id);
        
    //     $name = $this->input_post("name");
    //     $parent_id = $this->input_post("parent_id");
    //     $display_type = $this->input_post("fun_type");
        
    //     $filter = new Fun();
        
    //     if($name){
    //         $filter->setName($name);
    //     }
    //     if($parent_id){
    //         $filter->setParentId($parent_id);
    //     }
    //     if($display_type){
    //         if(!in_array($display_type, FunType::getFunTypeCode())){
    //             $this->_response(InputValidator::constructInvalidParamResponse('Invalid params.'));
    //             return false;
    //         }
    //         $filter->setDisplayType($display_type);
    //     }
        
    //     $limit = $this->_getLimit();
    //     $page = $this->_getPage();
    //     $isRoot = false;
        
    //     if($result = $this->_service->selectFun($filter, $isRoot, $limit, $page)){
            
    //         $parent_ids = array();
    //         foreach ($result->result as $entityFun){
    //             $parent_ids[] = $entityFun->getId();
    //         }
    //         $filter = new Fun();
    //         $collectionParent = $this->_service->selectFun($filter);
            
    //         $arrResult = array();
    //         foreach ($result->result as $entityFun) {
    //             $arrFun = $entityFun->getSelectedField(array('id','code','name','display_type','description','url','created_at','created_by_name'));
    //             $arrFun['parent_name'] = null;
    //             $parent_id = $entityFun->getParentId();
    //             if($parent_id){
    //                 if($entityParent = $collectionParent->result->getById($parent_id)){
    //                     $arrFun['parent_name'] = $entityParent->getName();
    //                 }
    //             }
    //             $arrResult[] = $arrFun;
    //         }
    //         $result->result = $arrResult;
            
    //         $this->_respondWithSuccessCode($this->_service->getResponseCode(), array("result" => $result));
    //         return true;
    //     }
    //     $this->_respondWithFailedCode($this->_service->getResponseCode());
    //     return false;
        
    // }

    public function addFun(){
        
        log_message("debug", "function Fun_admin->addFun begin");
        
        $admin_id = $this->_getAdminId(FunCode::ACCESS_MANAGE);

        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('code','name','fun_type', 'display_order'));
        
        $code = $this->input_post("code");
        $name = $this->input_post("name");
        $parent_id = $this->input_post("parent_id");
        $display_type = $this->input_post("fun_type");
        $display_order = $this->input_post("display_order");
        $description = $this->input_post("description");
        $access_type = "R";
        
        if($display_type){
            if(!in_array($display_type, FunType::getFunTypeCode())){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid params.'));
                return false;
            }
        }
        if($display_order){
            if(!is_numeric($display_order)){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid params.'));
                return false;
            }
        }
        
        $entityFun = new Fun();
        $entityFun->setId(GuidGenerator::generate());
        $entityFun->setCode($code);
        $entityFun->setName($name);
        $entityFun->setParentId($parent_id);
        $entityFun->setDisplayType($display_type);
        $entityFun->setDisplayOrder($display_order);
        $entityFun->setAccessType($access_type);
        $entityFun->setDescription($description);
        
        if($result = $this->_service->addFun($entityFun)){
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array("result" => $result));
            return true;
        }
        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }
    
    public function getFun(){
        
        log_message("debug", "function Fun_admin->getFun begin");
        
        $admin_id = $this->_getAdminId(FunCode::ACCESS_MANAGE);

        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('id'));
        
        $id = $this->input_post("id");
        
        $filter = new Fun();
        $filter->setId($id);
        if($result = $this->_service->selectFun($filter)){
            
            $result = $result->result->current();
            
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array("result" => $result));
            return true;
        }
        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function editFun(){
        
        log_message("debug", "function Fun_admin->editFun begin");
        
        $admin_id = $this->_getAdminId(FunCode::ACCESS_MANAGE);

        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('id','code','name','fun_type', 'display_order'));
        
        $id = $this->input_post("id");
        $code = $this->input_post("code");
        $name = $this->input_post("name");
        $parent_id = $this->input_post("parent_id");
        $display_type = $this->input_post("fun_type");
        $display_order = $this->input_post("display_order");
        $description = $this->input_post("description");
        $access_type = "R";
        
        if($display_type){
            if(!in_array($display_type, FunType::getFunTypeCode())){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid params.'));
                return false;
            }
        }
        if($display_order){
            if(!is_numeric($display_order)){
                $this->_response(InputValidator::constructInvalidParamResponse('Invalid params.'));
                return false;
            }
        }
        
        $entityFun = new Fun();
        $entityFun->setId($id);
        $entityFun->setCode($code);
        $entityFun->setName($name);
        $entityFun->setParentId($parent_id);
        $entityFun->setDisplayType($display_type);
        $entityFun->setDisplayOrder($display_order);
        $entityFun->setAccessType($access_type);
        $entityFun->setDescription($description);
        
        if($result = $this->_service->updateFun($entityFun)){
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array("result" => $result));
            return true;
        }
        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

    public function delFun(){
        
        log_message("debug", "function Fun_admin->delFun begin");
        
        $admin_id = $this->_getAdminId(FunCode::ACCESS_MANAGE);

        $this->_service->setUpdatedBy($admin_id);
        
        $this->required(array('id'));
        
        $id = $this->input_post("id");
        
        if($result = $this->_service->deleteFun($id)){
            $this->_respondWithSuccessCode($this->_service->getResponseCode(), array("result" => $result));
            return true;
        }
        $this->_respondWithFailedCode($this->_service->getResponseCode());
        return false;
    }

}
