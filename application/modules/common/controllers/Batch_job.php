<?php

use Common\Core\IpAddress;
use Common\Helper\ResponseHeader;
use AccountService\Common\MessageCode;
use Common\Helper\RequestHeader;
use Common\Helper\CronHelper;
use AccountService\Wxconfig\WxconfigService;
use AccountService\NewsDomainPool\NewsDomainPoolService;
use AccountService\AppDomainPool\AppDomainPoolService;
use AccountService\NewsVisit\NewsVisitEventConsumer;
use AccountService\NewsRead\NewsReadEventConsumer;
use AccountService\AdMateVisit\AdMateVisitEventConsumer;
use AccountService\AdMateClick\AdMateClickEventConsumer;
use AccountService\News\SystemNewsService;
use AccountService\NewsVisitToday\NewsVisitTodayService;

class Batch_job extends System_Base_Controller{

    function __construct()
    {
        parent::__construct();

    }
    
    // public function updateWeiXinAppToken(){

    //     $lockName = "updateWeiXinAppToken";
    //     $route = "job/wxapp/updatetoken";

    //     try {
            
    //         if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

    //             $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
    //             return false;
    //         }
            
    //         if (!$system_user_id = $this->_getUserProfileId()) {
    //             CronHelper::unlock($lockName);                
    //             //$this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
    //             return false;
    //         }

    //         $service = WxconfigService::build();
    //         $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    //         $service->setUpdatedBy($system_user_id);

    //         if ($service->refreshAccessToken()) {

    //             CronHelper::unlock($lockName);

    //             $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
    //             return true;
    //         }

    //         CronHelper::unlock($lockName);

    //         $this->_respondWithSuccessCode($service->getResponseCode());
    //         return false;
    //     }
    //     catch (\Exception $ex) {
            
    //         log_message('error', "updateWeiXinAppToken - error:" . $ex->getMessage());
            
    //         CronHelper::unlock($lockName);
            
    //         $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
    //         return false;
    //     }
    // }
 
    public function resetNewsDomainNum(){
        
        $lockName = "resetNewsDomainNum";
        $route = "job/newsdomain/resetnum";
        
        log_message('error', "resetNewsDomainNum - start");

        try {
            
            if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

                log_message('error', "resetNewsDomainNum - lock fail");
                
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            if (!$system_user_id = $this->_getUserProfileId()) {
                
                log_message('error', "resetNewsDomainNum - get  system user fail");
                
                CronHelper::unlock($lockName);
                
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            $service = NewsDomainPoolService::build();
            $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            $service->setUpdatedBy($system_user_id);

            if ($service->resetAllShowNum()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode($service->getResponseCode());
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "resetNewsDomainNum - error:" . $ex->getMessage());          
            
            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }
    
    public function resetAppDomainNum(){
        
        $lockName = "resetAppDomainNum";
        $route = "job/appomain/resetnum";
        
        log_message('error', "resetAppDomainNum - start");

        try {
            
            if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

                log_message('error', "resetAppDomainNum - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            if (!$system_user_id = $this->_getUserProfileId()) {
                
                log_message('error', "resetAppDomainNum - get  system user fail");
                
                CronHelper::unlock($lockName);
                
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            $service = AppDomainPoolService::build();
            $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            $service->setUpdatedBy($system_user_id);

            if ($service->resetAllShowNum()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode($service->getResponseCode());
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "resetAppDomainNum - error:" . $ex->getMessage());
            
            CronHelper::unlock($lockName);
            
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    // public function newsDomainPoolCheck(){
        
    //     $lockName = "newsDomainPoolCheck";
    //     $route = "job/newsdomain/check";
        
    //     log_message('error', "newsDomainPoolCheck - start");

    //     try {
            
    //         if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

    //             log_message('error', "newsDomainPoolCheck - lock fail");

    //             $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
    //             return false;
    //         }
            
    //         // if (!$system_user_id = $this->_getUserProfileId()) {
                
    //         //     log_message('error', "newsDomainPoolCheck - get  system user fail");
                
    //         //     CronHelper::unlock($lockName);
                
    //         //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
    //         //     return false;
    //         // }

    //         $service = NewsDomainPoolService::build();
    //         $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
    //         // $service->setUpdatedBy($system_user_id);

    //         if ($service->checkDomain()) {

    //             CronHelper::unlock($lockName);

    //             $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
    //             return true;
    //         }

    //         CronHelper::unlock($lockName);

    //         $this->_respondWithSuccessCode($service->getResponseCode());
    //         return false;
    //     }
    //     catch (\Exception $ex) {
            
    //         log_message('error', "newsDomainPoolCheck - error:" . $ex->getMessage());
            
    //         CronHelper::unlock($lockName);
            
    //         $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
    //         return false;
    //     }
    // }
    
    public function updateWeiXinDomainStatus(){
        
        $lockName = "updateWeiXinDomainStatus";
        $route = "job/wxapp/updatestatus";
        
        log_message('error', "updateWeiXinDomainStatus - start");

        try {
            
            if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

                log_message('error', "updateWeiXinDomainStatus - lock fail");
                
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            if (!$system_user_id = $this->_getUserProfileId()) {
                
                log_message('error', "updateWeiXinDomainStatus - get  system user fail");
                
                CronHelper::unlock($lockName);
                
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            $service = WxconfigService::build();
            $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            $service->setUpdatedBy($system_user_id);

            if ($service->updateWeiXinDomainStatus()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode($service->getResponseCode());
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "checkWxconfigDomainStatus - error:" . $ex->getMessage());          
            
            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }
    
    //处理新闻访问事件
    public function handleNewsVisitEvent(){
        
        $lockName = "handleNewsVisitEvent";
        $route = "job/news/visitEvent";
        
        log_message('debug', "handleNewsVisitEvent - start");

        try {
            
            if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

                log_message('debug', "handleNewsVisitEvent - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            // if (!$system_user_id = $this->_getUserProfileId()) {
                
            //     log_message('error', "handleNewsVisitEvent - get  system user fail");
                
            //     CronHelper::unlock($lockName);
                
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }

            $consumer = new NewsVisitEventConsumer();
            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            // $consumer->setUpdatedBy($system_user_id);
            
            if ($consumer->listenNewsVisitEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "handleNewsVisitEvent - error:" . $ex->getMessage());
            
            CronHelper::unlock($lockName);
            
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //处理新闻阅读事件
    public function handleNewsReadEvent(){
        
        $lockName = "handleNewsReadEvent";
        $route = "job/news/readEvent";
        
        log_message('debug', "handleNewsReadEvent - start");

        try {
            
            if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

                log_message('debug', "handleNewsReadEvent - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            // if (!$system_user_id = $this->_getUserProfileId()) {
                
            //     log_message('error', "handleNewsReadEvent - get  system user fail");
                
            //     CronHelper::unlock($lockName);
                
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }

            $consumer = new NewsReadEventConsumer();
            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            // $consumer->setUpdatedBy($system_user_id);
            
            if ($consumer->listenNewsReadEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }
////            $msg = new stdClass();
////            $msg->messageId = "AC11D78D14B53D4EAC699F4A2F305A12";
////            $msg->messageBodyMD5 = "7127D4D71193532AB49268600BADA45A";
////            $msg->messageBody =  ;
////            $msg->messageTag = null;
//            $msg = new stdClass();
//            $msg->visit_id = "887744";
//            $msg->info = json_encode(array("start" => 1596205211688,"page" => 1,"click_num" => 1,"slide_num" => 1,"shake" => 0,"charging" => false,"battery_level" => 0.8,"more_click" => 1,"banner_click" => 0,"ad_click" => 0,"hold_times" => 26640));
//            $consumer->doTask($msg);

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "handleNewsReadEvent - error:" . $ex->getMessage());
            
            CronHelper::unlock($lockName);
            
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //处理广告访问事件
    public function handleAdMateVisitEvent(){
        
        $lockName = "handleAdMateVisitEvent";
        $route = "job/ad/visitEvent";
        
        log_message('debug', "handleAdMateVisitEvent - start");

        try {
            
            if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

                log_message('debug', "handleAdMateVisitEvent - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            // if (!$system_user_id = $this->_getUserProfileId()) {
                
            //     log_message('error', "handleAdMateVisitEvent - get  system user fail");
                
            //     CronHelper::unlock($lockName);
                
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }

            $consumer = new AdMateVisitEventConsumer();
            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            // $consumer->setUpdatedBy($system_user_id);
            
//            $data = json_decode('{"visit_at":1593961243,"open_id":"o84JOwoFkNkyzgbrjA0iW1EzRjtw","uv_code":"541a62d37d5f2818871359175a436a5d","ip_code":29761147,"today_list":[{"visit_today_id":"26294","ad_id":"15","mate_id":"24","aduser_id":"30"},{"visit_today_id":"26295","ad_id":"14","mate_id":"4","aduser_id":"30"},{"visit_today_id":"26296","ad_id":"13","mate_id":"13","aduser_id":"30"},{"visit_today_id":"26297","ad_id":"12","mate_id":"12","aduser_id":"30"},{"visit_today_id":"26298","ad_id":"11","mate_id":"11","aduser_id":"30"},{"visit_today_id":"26299","ad_id":"10","mate_id":"10","aduser_id":"30"},{"visit_today_id":"26300","ad_id":"9","mate_id":"9","aduser_id":"30"},{"visit_today_id":"26301","ad_id":"24","mate_id":"8","aduser_id":"30"},{"visit_today_id":"26302","ad_id":"19","mate_id":"3","aduser_id":"30"},{"visit_today_id":"26303","ad_id":"23","mate_id":"9","aduser_id":"30"},{"visit_today_id":"26304","ad_id":"22","mate_id":"10","aduser_id":"30"},{"visit_today_id":"26305","ad_id":"21","mate_id":"11","aduser_id":"30"},{"visit_today_id":"26306","ad_id":"17","mate_id":"25","aduser_id":"30"},{"visit_today_id":"26307","ad_id":"20","mate_id":"11","aduser_id":"30"},{"visit_today_id":"26308","ad_id":"8","mate_id":"9","aduser_id":"30"},{"visit_today_id":"26309","ad_id":"26","mate_id":"8","aduser_id":"30"},{"visit_today_id":"26310","ad_id":"25","mate_id":"10","aduser_id":"30"},{"visit_today_id":"26311","ad_id":"16","mate_id":"1","aduser_id":"30"},{"visit_today_id":"26312","ad_id":"18","mate_id":"26","aduser_id":"30"},{"visit_today_id":"26313","ad_id":"6","mate_id":"5","aduser_id":"30"},{"visit_today_id":"26314","ad_id":"7","mate_id":"23","aduser_id":"30"}]}');
//            $consumer->doTask($data);
            
            if ($consumer->listenAdMateVisitEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "handleAdMateVisitEvent - error:" . $ex->getMessage());
            
            CronHelper::unlock($lockName);
            
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //处理广告点击事件
    public function handleAdMateClickEvent(){
        
        $lockName = "handleAdMateClickEvent";
        $route = "job/ad/clickEvent";
        
        log_message('debug', "handleAdMateClickEvent - start");

        try {
            
            if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

                log_message('debug', "handleAdMateClickEvent - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            // if (!$system_user_id = $this->_getUserProfileId()) {
                
            //     log_message('error', "handleAdMateClickEvent - get  system user fail");
                
            //     CronHelper::unlock($lockName);
                
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }

            $consumer = new AdMateClickEventConsumer();
            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            // $consumer->setUpdatedBy($system_user_id);
            
            if ($consumer->listenAdMateClickEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "handleAdMateClickEvent - error:" . $ex->getMessage());
            
            CronHelper::unlock($lockName);
            
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //采集草莓
    public function caomeiCollect(){
        
        $lockName = "caomeiCollect";
        $route = "job/collect/caomei";
        
        log_message('debug', "caomeiCollect - start");

        try {
            
            // if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

            //     log_message('debug', "caomeiCollect - lock fail");

            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }
            
            // if (!$system_user_id = $this->_getUserProfileId()) {
                
            //     log_message('error', "handleAdMateClickEvent - get  system user fail");
                
            //     CronHelper::unlock($lockName);
                
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }

            $sysNewsService = SystemNewsService::build();
            
            if ($sysNewsService->caomeiCollect()) {

                // CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            // CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "handleAdMateClickEvent - error:" . $ex->getMessage());
            
            // CronHelper::unlock($lockName);
            
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //采集好看
    public function haokanCollect(){

        // if(isset($_SERVER['REMOTE_ADDR'])){
        //     echo "REMOTE_ADDR: ".$_SERVER['REMOTE_ADDR'];
        // }

        // if(isset($_SERVER['HTTP_VIA'])){
        //     echo "HTTP_VIA: ".$_SERVER['HTTP_VIA'];
        // }
        
        
        // if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //     echo "HTTP_X_FORWARDED_FOR: ".$_SERVER['HTTP_X_FORWARDED_FOR'];
        // }


        // if(isset($_SERVER['HTTP_CLIENT_IP'])){
        //     echo "HTTP_CLIENT_IP: ".$_SERVER['HTTP_CLIENT_IP'];
        // }
        

        // exit();


        
        $lockName = "haokanCollect";
        $route = "job/collect/haokan";
        
        log_message('debug', "haokanCollect - start");

        try {
            
            // if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

            //     log_message('debug', "haokanCollect - lock fail");

            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }
            
            // if (!$system_user_id = $this->_getUserProfileId()) {
                
            //     log_message('error', "handleAdMateClickEvent - get  system user fail");
                
            //     CronHelper::unlock($lockName);
                
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }

            $sysNewsService = SystemNewsService::build();
            
            if ($sysNewsService->haokanCollect()) {

                // CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            // CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "handleAdMateClickEvent - error:" . $ex->getMessage());
            
            // CronHelper::unlock($lockName);
            
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }
    
    /**
     * 清除新闻天阅读历史
     * @return boolean
     */
    public function handleClearNewsVisitToday(){
        
        $lockName = "handleClearNewsVisitToday";
        $route = "job/data/news_visit_today";
        
        log_message('info', "handleClearNewsVisitToday - start");
        
        $this->log_benchmark("total_execution_time_start", "handleClearNewsVisitToday_start", "handleClearNewsVisitToday", "handleClearNewsVisitToday_start");
        
        try {
            
            if (($pid = CronHelper::lock($lockName, $route)) == FALSE) {

                log_message('error', "handleClearNewsVisitToday - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }
            
            // if (!$system_user_id = $this->_getUserProfileId()) {
                
            //     log_message('error', "newsDomainPoolCheck - get  system user fail");
                
            //     CronHelper::unlock($lockName);
                
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }
            
            $limit = $this->_getLimit();
            $page = $this->_getPage();
            $day = $this->input_get("day");
            
            if(empty($limit))
                $limit = 100;
            if(empty($day))
                $day = 3;


            $service = NewsVisitTodayService::build();
            $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            // $service->setUpdatedBy($system_user_id);
            
            if ($service->processRemoveNewsVisitData($limit, $day)) {

                log_message('info', "handleClearNewsVisitToday - end success");

                $this->log_benchmark("handleClearNewsVisitToday_start", "handleClearNewsVisitToday_end", "handleClearNewsVisitToday", "handleClearNewsVisitToday_end");

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }
            
            log_message('info', "handleClearNewsVisitToday - end");

            $this->log_benchmark("handleClearNewsVisitToday_start", "handleClearNewsVisitToday_end", "handleClearNewsVisitToday", "handleClearNewsVisitToday_end");

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode($service->getResponseCode());
            return false;
        }
        catch (\Exception $ex) {
            
            log_message('error', "handleClearNewsVisitToday - end error:" . $ex->getMessage());
            
            $this->log_benchmark("handleClearNewsVisitToday_start", "handleClearNewsVisitToday_end", "handleClearNewsVisitToday", "handleClearNewsVisitToday_end");

            CronHelper::unlock($lockName);
            
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }
    
}