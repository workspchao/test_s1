<?php

use AccountService\Common\MessageCode;
use Common\Core\IpAddress;
use Common\Helper\RequestHeader;
use Common\Helper\ResponseHeader;
use AccountService\NewsDomainPool\NewsDomainPoolService;
use Common\Helper\CronHelper;
use AccountService\NewsVisit\NewsVisitEventConsumer;
use AccountService\NewsRead\NewsReadEventConsumer;
use AccountService\AdMateVisit\AdMateVisitEventConsumer;
use AccountService\AdMateClick\AdMateClickEventConsumer;
use AccountService\TestEvent\TestEventConsumer;
use AccountService\TestEvent\TestEventProducer;
use Common\Core\BaseDateTime;
use AccountService\News\SystemNewsService;
use AccountService\Sms\SmsEventConsumer;
use AccountService\Wxconfig\WxconfigService;
use AccountService\UserRewardHis\UserRewardHisService;
use AccountService\NewsVisitToday\NewsVisitTodayService;
use AccountService\CashoutRequest\AdminCashoutRequestService;

class Batch_cli extends Cli_Base_Controller {

    //自动审核提现
    public function cashoutAutoApprove() {

        $lockName = "cashoutAutoApprove";
        
        log_message('debug', "$lockName - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {
                log_message('error', "$lockName - lock fail");
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            $service = AdminCashoutRequestService::build();
            $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            if($service->autoApprove()){

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }


            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode($service->getResponseCode());
            return false;
        } catch (\Exception $ex) {

            log_message('error', "$lockName - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }



    //今日收益加成结算
    public function todayIncomeAdditionSettle() {

        $lockName = "todayIncomeAdditionSettle";
        
        log_message('debug', "$lockName - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {
                log_message('error', "$lockName - lock fail");
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            $service = UserRewardHisService::build();
            $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            if($service->todayIncomeAdditionSettle()){

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }


            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode($service->getResponseCode());
            return false;
        } catch (\Exception $ex) {

            log_message('error', "$lockName - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    public function updateQyWxConfigToken() {

        $lockName = "updateQyWxConfigToken";
        
        log_message('debug', "$lockName - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {
                log_message('error', "$lockName - lock fail");
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            $service = WxconfigService::build();
            $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            if($service->refreshQyAccessToken()){

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }


            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode($service->getResponseCode());
            return false;
        } catch (\Exception $ex) {

            log_message('error', "$lockName - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    public function updateWeiXinDomainStatus(){
        
        $lockName = "updateWeiXinDomainStatus";
        log_message('debug', "$lockName - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {
                log_message('error', "$lockName - lock fail");
                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            $service = WxconfigService::build();
            $service->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            //$service->setUpdatedBy($system_user_id);
                
            if($service->updateWeiXinDomainStatus()){

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }


            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode($service->getResponseCode());
            return false;
        } catch (\Exception $ex) {

            log_message('error', "$lockName - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //短信发送处理
    public function handleSmsEvent() {

        $lockName = "handleSmsEvent";
        
        log_message('debug', "handleSmsEvent - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {

                log_message('error', "handleSmsEvent - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            // if (!$system_user_id = $this->_getUserProfileId()) {
            //     log_message('error', "handleSmsEvent - get  system user fail");
            //     CronHelper::unlock($lockName);
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }
            
            $consumer = new SmsEventConsumer();
            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
            $consumer->setTTL(NULL);
            // $consumer->setUpdatedBy($system_user_id);
            
            if ($consumer->listenSmsEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        } catch (\Exception $ex) {

            log_message('error', "handleSmsEvent - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //新闻内容访问
    public function handleNewsVisitEvent() {

        $lockName = "handleNewsVisitEvent";
        
        log_message('debug', "handleNewsVisitEvent - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {

                log_message('error', "handleNewsVisitEvent - lock fail");

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
            $consumer->setTTL(NULL);
            // $consumer->setUpdatedBy($system_user_id);
            
            if ($consumer->listenNewsVisitEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        } catch (\Exception $ex) {

            log_message('error', "handleNewsVisitEvent - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //新闻内容阅读
    public function handleNewsReadEvent() {
        
        $lockName = "handleNewsReadEvent";
        
        log_message('debug', "handleNewsReadEvent - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {

                log_message('error', "handleNewsReadEvent - lock fail");

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
            $consumer->setTTL(NULL);
            // $consumer->setUpdatedBy($system_user_id);
            
            if ($consumer->listenNewsReadEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        } catch (\Exception $ex) {

            log_message('error', "handleNewsReadEvent - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //广告浏览
    public function handleAdMateVisitEvent() {

        $lockName = "handleAdMateVisitEvent";
        
        log_message('debug', "handleAdMateVisitEvent - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {

                log_message('error', "handleAdMateVisitEvent - lock fail");

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
            $consumer->setTTL(NULL);
            // $consumer->setUpdatedBy($system_user_id);
            
            if ($consumer->listenAdMateVisitEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        } catch (\Exception $ex) {

            log_message('error', "handleAdMateVisitEvent - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }

    //广告点击
    public function handleAdMateClickEvent() {

        $lockName = "handleAdMateClickEvent";
        
        log_message('debug', "handleAdMateClickEvent - start");

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {

                log_message('error', "handleAdMateClickEvent - lock fail");

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
            $consumer->setTTL(NULL);

            // $consumer->setUpdatedBy($system_user_id);
            
            if ($consumer->listenAdMateClickEvent()) {

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        } catch (\Exception $ex) {

            log_message('error', "handleAdMateClickEvent - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }
    

    //草莓采集
    public function caomeiCollect() {

        $lockName = "caomeiCollect";
        
        log_message('debug', "caomeiCollect - start".microtime(true));

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {

                log_message('error', "caomeiCollect - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            // if (!$system_user_id = $this->_getUserProfileId()) {
            //     log_message('error', "caomeiCollect - get  system user fail");
            //     CronHelper::unlock($lockName);
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }

            
            $sysNewsServ = SystemNewsService::build();
            if ($sysNewsServ->caomeiCollect()) {


                log_message('debug', "caomeiCollect - end".microtime(true));
                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        } catch (\Exception $ex) {

            log_message('error', "caomeiCollect - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }


    //好看采集
    public function haokanCollect() {

        $lockName = "haokanCollect";
        
        log_message('debug', "haokanCollect - start".microtime(true));

        try {

            if (($pid = CronHelper::lock($lockName)) == FALSE) {

                log_message('error', "haokanCollect - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            // if (!$system_user_id = $this->_getUserProfileId()) {
            //     log_message('error', "haokanCollect - get  system user fail");
            //     CronHelper::unlock($lockName);
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }

            
            $sysNewsServ = SystemNewsService::build();
            if ($sysNewsServ->haokanCollect()) {


                log_message('debug', "haokanCollect - end".microtime(true));
                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        } catch (\Exception $ex) {

            log_message('error', "haokanCollect - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }
    
    //清理新闻日阅读历史
    public function handleClearNewsVisitToday(){
        
        $lockName = "handleClearNewsVisitToday";
        
        log_message('debug', "$lockName - start");

        try {
            
            if (($pid = CronHelper::lock($lockName)) == FALSE) {

                log_message('error', "$lockName - lock fail");

                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
                return false;
            }

            // if (!$system_user_id = $this->_getUserProfileId()) {
            //     log_message('error', "caomeiCollect - get  system user fail");
            //     CronHelper::unlock($lockName);
            //     $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
            //     return false;
            // }
            
            $limit = $this->getArgument(1);
            $day = $this->getArgument(2);
            if(empty($limit))
                $limit = 100;
            if(empty($day))
                $day = 3;

            $service = NewsVisitTodayService::build();
            // $service->setUpdatedBy($system_user_id);
            $service->setBenchmark($this->benchmark);
            $service->setBenchmarkFun($lockName);
            
            
            if ($service->processRemoveNewsVisitData($limit, $day)) {

                log_message('info', "handleClearNewsVisitToday - end success");

                CronHelper::unlock($lockName);

                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
                return true;
            }
            
            log_message('debug', "$lockName - end - fail");

            CronHelper::unlock($lockName);
            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
        catch (\Exception $ex) {

            log_message('error', "$lockName - error:" . $ex->getMessage());

            CronHelper::unlock($lockName);

            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
            return false;
        }
    }
    
//    //多消费者测试1
//    public function listenTest1(){
//        
//        $lockName = "listenTest1";
//        
//        log_message('error', "listenTest1 - start");
//
//        try {
//
//            if (($pid = CronHelper::lock($lockName)) == FALSE) {
//
//                log_message('error', "listenTest1 - lock fail");
//
//                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                return false;
//            }
//
//             if (!$system_user_id = $this->_getUserProfileId()) {
//                 
//                 log_message('error', "listenTest1 - get system user fail");
//                 
//                 CronHelper::unlock($lockName);
//                 $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                 return false;
//             }
//
//            $consumer = new TestEventConsumer();
//            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
//            $consumer->setTTL(NULL);
//
//            $consumer->setUpdatedBy($system_user_id);
//            
//            if ($consumer->listenEvent()) {
//
//                CronHelper::unlock($lockName);
//
//                log_message('error', "listenTest1 - end - success");
//
//                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
//                return true;
//            }
//            
//            log_message('error', "listenTest1 - end - failed");
//
//            CronHelper::unlock($lockName);
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        } catch (\Exception $ex) {
//
//            log_message('error', "listenTest1 - error:" . $ex->getMessage());
//
//            CronHelper::unlock($lockName);
//
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        }
//    }
//    
//    //多消费者测试2
//    public function listenTest2(){
//        
//        $lockName = "listenTest2";
//        
//        log_message('error', "listenTest2 - start");
//
//        try {
//
//            if (($pid = CronHelper::lock($lockName)) == FALSE) {
//
//                log_message('error', "listenTest2 - lock fail");
//
//                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                return false;
//            }
//
//             if (!$system_user_id = $this->_getUserProfileId()) {
//                 
//                 log_message('error', "listenTest2 - get system user fail");
//                 
//                 CronHelper::unlock($lockName);
//                 $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                 return false;
//             }
//
//            $consumer = new TestEventConsumer();
//            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
//            $consumer->setTTL(NULL);
//
//            $consumer->setUpdatedBy($system_user_id);
//            
//            if ($consumer->listenEvent()) {
//
//                CronHelper::unlock($lockName);
//
//                log_message('error', "listenTest2 - end - success");
//
//                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
//                return true;
//            }
//            
//            log_message('error', "listenTest2 - end - failed");
//
//            CronHelper::unlock($lockName);
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        } catch (\Exception $ex) {
//
//            log_message('error', "listenTest2 - error:" . $ex->getMessage());
//
//            CronHelper::unlock($lockName);
//
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        }
//    }
//    
//    //多消费者测试3
//    public function listenTest3(){
//        
//        $lockName = "listenTest3";
//        
//        log_message('error', "listenTest3 - start");
//
//        try {
//
//            if (($pid = CronHelper::lock($lockName)) == FALSE) {
//
//                log_message('error', "listenTest3 - lock fail");
//
//                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                return false;
//            }
//
//             if (!$system_user_id = $this->_getUserProfileId()) {
//                 
//                 log_message('error', "listenTest3 - get system user fail");
//                 
//                 CronHelper::unlock($lockName);
//                 $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                 return false;
//             }
//
//            $consumer = new TestEventConsumer();
//            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
//            $consumer->setTTL(NULL);
//
//            $consumer->setUpdatedBy($system_user_id);
//            
//            if ($consumer->listenEvent()) {
//
//                CronHelper::unlock($lockName);
//
//                log_message('error', "listenTest3 - end - success");
//
//                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
//                return true;
//            }
//            
//            log_message('error', "listenTest3 - end - failed");
//
//            CronHelper::unlock($lockName);
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        } catch (\Exception $ex) {
//
//            log_message('error', "listenTest3 - error:" . $ex->getMessage());
//
//            CronHelper::unlock($lockName);
//
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        }
//    }
//    
//    //多消费者测试4
//    public function listenTest4(){
//        
//        $lockName = "listenTest4";
//        
//        log_message('error', "listenTest4 - start");
//
//        try {
//
//            if (($pid = CronHelper::lock($lockName)) == FALSE) {
//
//                log_message('error', "listenTest4 - lock fail");
//
//                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                return false;
//            }
//
//             if (!$system_user_id = $this->_getUserProfileId()) {
//                 
//                 log_message('error', "listenTest4 - get system user fail");
//                 
//                 CronHelper::unlock($lockName);
//                 $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                 return false;
//             }
//
//            $consumer = new TestEventConsumer();
//            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
//            $consumer->setTTL(NULL);
//
//            $consumer->setUpdatedBy($system_user_id);
//            
//            if ($consumer->listenEvent()) {
//
//                CronHelper::unlock($lockName);
//
//                log_message('error', "listenTest4 - end - success");
//
//                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
//                return true;
//            }
//            
//            log_message('error', "listenTest4 - end - failed");
//
//            CronHelper::unlock($lockName);
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        } catch (\Exception $ex) {
//
//            log_message('error', "listenTest4 - error:" . $ex->getMessage());
//
//            CronHelper::unlock($lockName);
//
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        }
//    }
//    
//    //多消费者测试5
//    public function listenTest5(){
//        
//        $lockName = "listenTest5";
//        
//        log_message('error', "listenTest5 - start");
//
//        try {
//
//            if (($pid = CronHelper::lock($lockName)) == FALSE) {
//
//                log_message('error', "listenTest5 - lock fail");
//
//                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                return false;
//            }
//
//             if (!$system_user_id = $this->_getUserProfileId()) {
//                 
//                 log_message('error', "listenTest5 - get system user fail");
//                 
//                 CronHelper::unlock($lockName);
//                 $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                 return false;
//             }
//
//            $consumer = new TestEventConsumer();
//            $consumer->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
//            $consumer->setTTL(NULL);
//
//            $consumer->setUpdatedBy($system_user_id);
//            
//            if ($consumer->listenEvent()) {
//
//                CronHelper::unlock($lockName);
//
//                log_message('error', "listenTest5 - end - success");
//
//                $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_PASSED);
//                return true;
//            }
//            
//            log_message('error', "listenTest5 - end - failed");
//
//            CronHelper::unlock($lockName);
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        } catch (\Exception $ex) {
//
//            log_message('error', "listenTest5 - error:" . $ex->getMessage());
//
//            CronHelper::unlock($lockName);
//
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        }
//    }
//    
//    public function publishTest(){
//        
//        $lockName = "publishTest";
//        
//        log_message('error', "publishTest - start");
//
//        try {
//
//            if (($pid = CronHelper::lock($lockName)) == FALSE) {
//
//                log_message('error', "publishTest - lock fail");
//
//                $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                return false;
//            }
//
//             if (!$system_user_id = $this->_getUserProfileId()) {
//                 
//                 log_message('error', "publishTest - get system user fail");
//                 
//                 CronHelper::unlock($lockName);
//                 $this->_respondWithCode(false, MessageCode::CODE_JOB_PROCESS_LOCKED, ResponseHeader::HEADER_NOT_FOUND);
//                 return false;
//             }
//
//            for($i = 0; $i < 100; $i++){
//                $id = $i . "|" . Common\Core\BaseDateTime::now()->getString();
//                $id .= "|" . Common\Helper\GuidGenerator::generate();
//                TestEventProducer::publishTest($id);
//            }
//            
//            log_message('error', "publishTest - end - failed");
//
//            CronHelper::unlock($lockName);
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        }
//        catch (\Exception $ex) {
//
//            log_message('error', "publishTest - error:" . $ex->getMessage());
//
//            CronHelper::unlock($lockName);
//
//            $this->_respondWithSuccessCode(MessageCode::CODE_JOB_PROCESS_FAILED);
//            return false;
//        }
//    }
//    
}
