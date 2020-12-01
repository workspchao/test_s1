<?php

namespace AccountService\CoreConfigData;

class CoreConfigType
{
    //access token period
    const ADMIN_SESSION_PERIOD = 'admin_session_period';
    const USER_SESSION_PERIOD = 'user_session_period';
    
    //black list(ip and user)
    const MAX_CONSECUTIVE_LOGIN_PER_IP_PERIOD = 'max_consecutive_login_per_ip_period';
    const MAX_CONSECUTIVE_LOGIN_PER_IP = 'max_consecutive_login_per_ip';
    const MAX_CONSECUTIVE_LOGIN_PER_USER_PERIOD = 'max_consecutive_login_per_user_period';
    const MAX_CONSECUTIVE_LOGIN_PER_USER = 'max_consecutive_login_per_user';

    
    //otp period
    const OTP_EMAIL_PERIOD = 'otp_email_period';
    const OTP_SMS_PERIOD = 'otp_sms_period';
    
    //时间
    const OTP_SMS_LIMIT_PERIOD = 'otp_sms_resend_restriction';
    //次数
    const OTP_SMS_LIMIT = 'otp_no_sms_limit';
    
    const OTP_RESEND_PERIOD = 'otp_resend_period';
    
    //relay profit percentage
    const RELAY_PROFIT_L1_PERCENTAGE = 'relay_profit_l1_percentage';
    const RELAY_PROFIT_L2_PERCENTAGE = 'relay_profit_l2_percentage';
    
    //friend_profit_rule
    const FRIEND_PROFIT_RULE = 'friend_profit_rule';
    
    //域名配置
    
    //新闻域名每天分享最大次数
    const NEWS_DOMAIN_A_MAX_TODAY_SHOW_NUM = 'news_domain_a_max_today_share_num';
    const NEWS_DOMAIN_B_MAX_TODAY_USE_NUM  = 'news_domain_b_max_today_share_num';
    const NEWS_DOMAIN_C_MAX_TODAY_USE_NUM  = 'news_domain_c_max_today_share_num';
    const NEWS_DOMAIN_D_MAX_TODAY_USE_NUM  = 'news_domain_d_max_today_share_num';
    
    //APP推广域名每天分享最大次数
    const APP_DOMAIN_E_MAX_TODAY_SHOW_NUM = 'app_domain_e_max_today_share_num';
    const APP_DOMAIN_F_MAX_TODAY_USE_NUM  = 'app_domain_f_max_today_use_num';
    
    //IOS分享中转链接
    const IOS_SHARE_URL         = 'ios_share_url';
    //APP LOGO地址
    const APP_LOGO_URL = 'app_logo_url';
    //APP 分享标题文案
    const APP_SHARE_TITLE = 'app_share_title';
    //APP 分享描述文案
    const APP_SHARE_DESC = 'app_share_desc';
    
    //APP下载链接
    const APP_DOWNLOAD_URL_IOS  = 'app_download_url_ios';
    const APP_DOWNLOAD_URL_ANDROID  = 'app_download_url_android';
    
    //好友提醒
    const FRIEND_REMINDER = 'friend_reminder';

    //提现时间
    const CASHOUT_START_TIME   = 'cashout_start_time';
    const CASHOUT_END_TIME   = 'cashout_end_time';


    const WX_CASHOUT_MCH_ID = "wx_cashout_mch_id";
    const WX_CASHOUT_MCH_SECRET = "wx_cashout_mch_secret";
    const ALI_CASHOUT_APP_ID = "ali_cashout_app_id";


    //奖品分配概率配置
    const REWARD_PERCENTAGE = "reward_percentage";    



    //扣量配置
    const REGISTER_HOUR_FOR_DEDU_PERCENT        = 'register_hour_for_dedu_percent';
    const REGISTER_HOUT_FOR_NO_DEDU_NUM         = 'register_hour_for_no_dedu_num';
    const DEDU_PERCENT_MORE_THAN_REGISTER_HOUR  = 'dedu_percent_more_than_register_hour';
    const DEDU_PERCENT_LESS_THAN_REGISTER_HOUR  = 'dedu_percent_less_than_register_hour';
    const NO_DEDU_NUM_MORE_THAN_REGISTER_HOUR   = 'no_dedu_num_more_than_register_hour';
    const NO_DEDU_NUM_LESS_THAN_REGISTER_HOUR   = 'no_dedu_num_less_than_register_hour';
    // const NEWS_READ_VALID_SECONDS               = 'news_read_valid_seconds';

    const REGISTER_HOUR_FOR_NEWS_READ           = 'register_hour_for_news_read';
    const NEWS_READ_MORE_THAN_REGISTER_HOUR     = 'news_read_more_than_register_hour';
    const NEWS_READ_LESS_THAN_REGISTER_HOUR     = 'news_read_less_than_register_hour';

    const PER_SHARE_MAX_VALID_HOURS             = 'per_share_max_valid_hours';
    const PERIOD_DEDU_CONFIG                    = 'period_dedu_config';
    const PER_SHARE_THRESHOLD_MINUTE            = 'per_share_threshold_minute';
    const PER_SHARE_THRESHOLD_VALID_NUM         = 'per_share_threshold_valid_num';


    //签到配置
    const SIGN_PERIOD_CONFIG = "sign_period_config";
    const SIGN_AMOUNT        = "sign_amount";


    //抽奖时段配额配置
    const REWARD_PERIOD_QUOTA = "reward_period_quota";

    
    //刷子IP地址库过滤开关(WZDB_COMMON)
    const IP_POOL_FILTER_SWITCH = 'ip_pool_filter_switch';



    //openid 自动过滤规则
    const OPENID_FILTER_RULE = "openid_filter_rule";

    
    public static function getDeduConfigCodes(){
        
        $codes   = array(
                self::REGISTER_HOUR_FOR_DEDU_PERCENT,
                self::REGISTER_HOUT_FOR_NO_DEDU_NUM,
                self::DEDU_PERCENT_MORE_THAN_REGISTER_HOUR,
                self::DEDU_PERCENT_LESS_THAN_REGISTER_HOUR,
                self::NO_DEDU_NUM_MORE_THAN_REGISTER_HOUR,
                self::NO_DEDU_NUM_LESS_THAN_REGISTER_HOUR,
                self::REGISTER_HOUR_FOR_NEWS_READ,
                self::NEWS_READ_MORE_THAN_REGISTER_HOUR,
                self::NEWS_READ_LESS_THAN_REGISTER_HOUR,
                self::PER_SHARE_MAX_VALID_HOURS,
                self::PERIOD_DEDU_CONFIG,
                self::PER_SHARE_THRESHOLD_MINUTE,
                self::PER_SHARE_THRESHOLD_VALID_NUM,
            );
        

        return $codes;
    }


    public static function getAppConfigCodes(){
        
        $codes   = array(
                self::IOS_SHARE_URL,
                self::APP_LOGO_URL,
                self::APP_SHARE_TITLE,
                self::APP_SHARE_DESC,
                self::RELAY_PROFIT_L1_PERCENTAGE,
                self::RELAY_PROFIT_L2_PERCENTAGE,
                self::CASHOUT_START_TIME,
                self::CASHOUT_END_TIME,
                self::WX_CASHOUT_MCH_ID,
                self::WX_CASHOUT_MCH_SECRET,
                self::ALI_CASHOUT_APP_ID,
                self::SIGN_PERIOD_CONFIG,
                self::SIGN_AMOUNT,
                self::REWARD_PERIOD_QUOTA,
                self::REWARD_PERCENTAGE,
                self::OPENID_FILTER_RULE
            );
        

        return $codes;
    }


    
//
//    const OTP_EMAIL_SUBJECT = 'otp_email_subject';
//    const OTP_EMAIL_MESSAGE = 'otp_email_message';
//    const OTP_SMS_MESSAGE = 'otp_sms_message';
//    const EMAIL_SENDER_NAME = 'wlc_email_message_subject';
//    const WLC_EMAIL_MESSAGE_SUB = 'wlc_email_message_subject';
//    const WLC_APP_EMAIL_MESSAGE_BODY = 'wlc_app_email_message_body';
//    const WLC_NONAPP_EMAIL_MESSAGE_BODY = 'wlc_nonapp_email_message_body';
//    const WLC_APP_MESSAGE = 'wlc_app_message';
//    const WLC_NONAPP_MESSAGE = 'wlc_nonapp_message';
//
//    const PASSWORD_CHANGED_EMAIL_SUBJECT = 'password_changed_email_subject';
//    const PASSWORD_CHANGED_EMAIL_BODY = 'password_changed_email_body';
//    const PASSWORD_CHANGED_EMAIL_MESSAGE = 'password_changed_message';
//
//    const MOBILE_CHANGED_EMAIL_SUBJECT = 'mobile_changed_email_subject';
//    const MOBILE_CHANGED_EMAIL_BODY = 'mobile_changed_email_body';
//    const MOBILE_CHANGED_MESSAGE = 'mobile_change_message';
//
//    const ACCOUNT_VERIFIED_EMAIL_SUBJECT = 'account_verified_email_subject';
//    const ACCOUNT_VERIFIED_EMAIL_BODY = 'account_verified_email_body';
//    const ACCOUNT_VERIFIED_MESSAGE = 'account_verified_message';
//
//    const PROFILE_EDITED_EMAIL_SUBJECT = 'profile_edited_email_subject';
//    const PROFILE_EDITED_EMAIL_BODY = 'profile_edited_email_body';
//    const PROFILE_EDITED_MESSAGE = 'profile_edited_message';
//
//    const TRANSACTION_SESSION_PERIOD = 'transaction_session_period';
//    const OTP_SESSION_PERIOD = 'otp_session_period';
//    const TRANSACTION_OTP_SESSION_PERIOD = 'transaction_otp_session_period';
//    const USER_AUTHORIZATOIN_PERIOD = 'user_authorization_period';

//
//    

//    
//    const ADMIN_PASSWORD_EXPIRATION_IN_DAY = 'admin_password_expiration';
//    const ADMIN_OLD_PASSWORD_COUNT = 'admin_old_password_count';
//
    
    
}
