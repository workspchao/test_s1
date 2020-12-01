<?php

use Common\Core\IpAddress;
use Common\Core\BaseDateTime;
use Common\Helper\ResponseHeader;
use AccountService\AccessToken\AccessTokenService;
use AccountService\AccessToken\AccessToken;
use AccountService\VersionControl\VersionControlService;
use AccountService\VersionControl\VersionControl;
use AccountService\UserProfile\UserProfileService;
use AccountService\UserProfile\UserProfile;
use AccountService\LoginAccount\LoginAccountService;
use AccountService\LoginAccount\LoginAccount;
use AccountService\LoginAccount\LoginAccountLoginType;
use Common\Helper\IniWriter;
use Common\Helper\InputValidator;
use Common\Helper\RandomCodeGenerator;
use AccountService\IncrementTable\IncrementIDService;
use AccountService\IncrementTable\IncrementIDAttribute;
use AccountService\Common\MessageCode;
use Common\ValueObject\PasswordObj;
use AccountService\NewsCategory\NewsCategory;
use AccountService\NewsCategory\NewsCategoryService;
use AccountService\CoreConfigData\CoreConfigDataService;
use AccountService\CoreConfigData\CoreConfigType;
use AccountService\Wxconfig\WxconfigService;
use AccountService\Wxconfig\WxconfigType;
use AccountService\Box\BoxService;
use AccountService\Box\Box;

class Common extends Base_Controller {

    protected $_service;
    protected $_serviceIncrementID;
    protected $_serviceUserProfile;
    protected $_serviceLoginAccount;
    protected $_serviceAccessToken;
    protected $_serviceVersionControl;
    protected $_serviceNewsCategory;
    protected $_serviceCoreConfigData;
    protected $_serviceWxconfig;
    protected $_serviceBox;

    function __construct() {
        
        parent::__construct();
        
        $this->_authoriseClient();
        
    }
    
    private function getServiceIncrementID(){
        if(!$this->_serviceIncrementID){
            $this->_serviceIncrementID = IncrementIDService::build();
        }
        $this->_serviceIncrementID->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceIncrementID->setUpdatedBy(0);
        return $this->_serviceIncrementID;
    }
    
    private function getServiceUserProfile(){
        if(!$this->_serviceUserProfile){
            $this->_serviceUserProfile = UserProfileService::build();
        }
        $this->_serviceUserProfile->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceUserProfile->setUpdatedBy(0);
        return $this->_serviceUserProfile;
    }
    
    private function getServiceLoginAccount(){
        if(!$this->_serviceLoginAccount){
            $this->_serviceLoginAccount = LoginAccountService::build();
        }
        $this->_serviceLoginAccount->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceLoginAccount->setUpdatedBy(0);
        return $this->_serviceLoginAccount;
    }
    
    private function getServiceAccessToken(){
        if(!$this->_serviceAccessToken){
            $this->_serviceAccessToken = AccessTokenService::build();
        }
        $this->_serviceAccessToken->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceAccessToken->setUpdatedBy(0);
        return $this->_serviceAccessToken;
    }
    
    private function getServiceVersionControl(){
        if(!$this->_serviceVersionControl){
            $this->_serviceVersionControl = VersionControlService::build();
        }
        $this->_serviceVersionControl->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceVersionControl->setUpdatedBy(0);
        return $this->_serviceVersionControl;
    }
    
    private function getServiceNewsCategory(){
        if(!$this->_serviceNewsCategory){
            $this->_serviceNewsCategory = NewsCategoryService::build();
        }
        $this->_serviceNewsCategory->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceNewsCategory->setUpdatedBy(0);
        return $this->_serviceNewsCategory;
    }
    
    private function getServiceCoreConfigData(){
        if(!$this->_serviceCoreConfigData){
            $this->_serviceCoreConfigData = CoreConfigDataService::build();
        }
        $this->_serviceCoreConfigData->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceCoreConfigData->setUpdatedBy(0);
        return $this->_serviceCoreConfigData;
    }
    
    private function getServiceWxconfig(){
        if(!$this->_serviceWxconfig){
            $this->_serviceWxconfig = WxconfigService::build();
        }
        $this->_serviceWxconfig->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceWxconfig->setUpdatedBy(0);
        return $this->_serviceWxconfig;
    }

    private function getServiceBox(){
        if(!$this->_serviceBox){
            $this->_serviceBox = BoxService::build();
        }
        $this->_serviceBox->setIpAddress(IpAddress::fromString($this->_getIpAddress()));
        $this->_serviceBox->setUpdatedBy(0);
        return $this->_serviceBox;
    }
    
    public function versionData(){
        
        $result = array(
            'news_category' => array(),
            'video_category' => array(),
            'relay_profit_percentage' => null,
            'friend_profit_rule' => null,
            'app_version' => null,
            'wxapp' => null,
            'cashout_profit_config' => array()
            );
        
        //news category
        $serviceNewsCategory = $this->getServiceNewsCategory();
        if($arrayNewsCategory = $serviceNewsCategory->getAllNewsCategoryFromCache()){
            $result['news_category'] = $arrayNewsCategory;
        }
        if($arrayVideoCategory = $serviceNewsCategory->getAllVideoCategoryFromCache()){
            $result['video_category'] = $arrayVideoCategory;
        }
        
        //relay profit percentage
        // $relay_profit_per = array("level_one" => null, "level_two" => null);
        // $serviceCoreConfigData = $this->getServiceCoreConfigData();
        // if($level1_rate = $serviceCoreConfigData->getConfig(CoreConfigType::RELAY_PROFIT_L1_PERCENTAGE)){
        //     $relay_profit_per['level_one'] = $level1_rate;
        // }
        // if($level2_rate = $serviceCoreConfigData->getConfig(CoreConfigType::RELAY_PROFIT_L2_PERCENTAGE)){
        //     $relay_profit_per['level_two'] = $level2_rate;
        // }
        // $result['relay_profit_percentage'] = $relay_profit_per;
        
        // //friend_profit_rule
        // if($rule = $serviceCoreConfigData->getConfig(CoreConfigType::FRIEND_PROFIT_RULE)){
        //     $result['friend_profit_rule'] = $rule;
        // }
        
        //app_versionF
        $app_id = $this->clientInfo['app_id'];
        $app_version = array('version' => null, 'fix_version' => null);
        $serviceVersionControl = $this->getServiceVersionControl();
        if($entityVersionControl = $serviceVersionControl->getVersionControlByAppId($app_id)){
            $app_version['version'] = $entityVersionControl->getVersion();
            $app_version['fix_version'] = $entityVersionControl->getHotVersion();
        }
        $result['app_version'] = $app_version;
        
        $serviceWxconfig = $this->getServiceWxconfig();
        if($entityWxconfig = $serviceWxconfig->getActiveApp(WxconfigType::ZKLOGIN)){
            $result['wxapp'] = $entityWxconfig->getAppId();
        }


        $result['ali_mobile_auth'] = "dlt16/rPGivx2nXn4VF2tQ4Y9EnATvl8xTdNor1R0IhZJLifBzSUr6rV/RZWYWZF/D5CvaYH22azIQ3fI+KPlg8F6Qb64B69McACwL26zvbK5+LxDxzHPmm7kkprVO7JbMKL8blp5FNr6lYBFepoWx9/jnHcCH32gf+gcBpxrigb6m8ge3qsCycBmO3MNJF8uvGldF5d4HB3D37usnlrfyxKkd+WKzk3vLm374DdCY/Cwkv86xee17/hXuRrz1vNGst4/ghYneWHXdEhP0KeYlNv1r7Fqsw/";

        // //收徒奖励
        // $serviceCashoutProfitConfig = $this->getServiceCashoutProfitConfig();
        // if($arrCashoutProfitConfig = $serviceCashoutProfitConfig->selectCashoutProfitConfig()){
        //     $result['cashout_profit_config'] = $arrCashoutProfitConfig->result->getSelectedField(array('times','amount'));
        // }
        
        $servBox = $this->getServiceBox();

        $boxFilter = new Box();
        if($boxCollection = $servBox->selectBox($boxFilter)){

            foreach ($boxCollection->result as $key => $value) {

                $box = array();
                $type = $value->getType();
                $amount = $value->getAmount();

                if($type <= 3){
                    $box['times_limit'] = $type;
                    $box['cashout_amount_limit'] = 0;
                }else if($type == 4){
                    $box['times_limit'] = 0;
                    $box['cashout_amount_limit'] = 100;
                }else{
                    continue;
                }

                $box['amount'] = $amount;
                $result['cashout_profit_config'][] = $box;
            }

            // $result['cashout_profit_config'] = $boxCollection->result->getSelectedField(array('type','amount'));
        }

        $this->_respondWithSuccessCode(MessageCode::CODE_VERSION_CONTROL_GET_SUCCESS, array('result' => $result));
        return true;
    }
    

    public function customerService(){
        //$result = array("account" => "QKBkefu003", "qrcode" => "http://commjqskj30.oss-cn-beijing.aliyuncs.com/public/images/app/customer_service.png", "business" => "97687682");
        $result = array("account" => "", "qrcode" => "http://commjqskj30.oss-cn-beijing.aliyuncs.com/public/images/app/customer_service03.png", "business" => "97687682");
        $this->_respondWithSuccessCode(MessageCode::CODE_VERSION_CONTROL_GET_SUCCESS, array('result' => $result));
        return true;
    }
}
