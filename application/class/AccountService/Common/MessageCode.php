<?php

namespace AccountService\Common;

class MessageCode {

    const CODE_INIT_INI_FILE_SAVE_FAIL              = 1000;
    const CODE_INIT_SYSTEM_ALREADY_INIT             = 1001;
    
    const CODE_INIT_USER_ALREADY_EXISTS             = 1010;
    const CODE_INIT_USER_CREATE_FAIL                = 1011;
    const CODE_INIT_USER_NOT_FOUND                  = 1012;
    const CODE_INIT_LOGIN_ACCOUNT_ALREADY_EXISTS    = 1013;
    const CODE_INIT_LOGIN_ACCOUNT_CREATE_FAIL       = 1014;
    const CODE_INIT_LOGIN_ACCOUNT_NOT_FOUND         = 1015;
    const CODE_INIT_TOKEN_CREATE_FAIL               = 1016;
    const CODE_INIT_TOKEN_NOT_FOUND                 = 1017;
    const CODE_INIT_CLIENT_APP_ALREADY_EXISTS       = 1018;
    const CODE_INIT_CLIENT_APP_CREATE_FAIL          = 1019;
    const CODE_INIT_CLIENT_APP_NOT_FOUND            = 1020;
    
    
    const CODE_INIT_FUN_ALREADY_EXISTS              = 1034;
    const CODE_INIT_FUN_CREATE_FAIL                 = 1035;
    const CODE_INIT_FUN_NOT_FOUND                   = 1036;
    
    const CODE_INIT_USER_FUN_ALREADY_EXISTS         = 1037;
    const CODE_INIT_USER_FUN_CREATE_FAIL            = 1038;
    const CODE_INIT_USER_FUN_NOT_FOUND              = 1039;
    
    const CODE_PASSWORD_TOO_WEAK                    = 1200;
    const CODE_CLIENT_NOT_AUTHORISED                = 1210;
    const CODE_CLIENT_AUTHORISED                    = 1211;
    const CODE_VERSION_OK                           = 1212;
    const CODE_OUTDATED_VERSION                     = 1213;
    const CODE_INVALID_ACCESS_TOKEN                 = 1220;
    const CODE_USER_NOT_ACCESSIBLE                  = 1221;
    const CODE_USER_IS_ACCESSIBLE                   = 1222;
    const CODE_INVALID_MOBILE_NUMBER                = 1223;
    const CODE_INVALID_INVITE_CODE                  = 1224;
    const CODE_JOB_PROCESS_LOCKED                   = 1225;
    const CODE_JOB_PROCESS_PASSED                   = 1226;
    const CODE_JOB_PROCESS_FAILED                   = 1227;
    const CODE_FREQUENT_ACTION                      = 1228;
    
    const CODE_REGISTER_OTP_SEND                    = 1230;
    const CODE_REGISTER_OTP_INVALID                 = 1231;
    const CODE_REGISTER_OTP_VERIFIED                = 1232;
    const CODE_PREVIOUS_OTP_ACTIVE                  = 1233;
    const CODE_OTP_SEND_FAIL                        = 1234;
    const CODE_OTP_SEND_EXCEED_RETRY                = 1235;
    const CODE_OTP_HAS_BEEN_SENT                    = 1236;
    const CODE_OTP_VERIFY_FAIL                      = 1237;
    const CODE_OTP_VERIFY_SUCCESS                   = 1238;
    
    const CODE_GENERATE_ACCOUNTID_FAIL              = 1240;
    const CODE_USER_ALREADY_EXISTS                  = 1241;
    const CODE_USER_CREATE_SUCCESS                  = 1242;
    const CODE_MOBILE_NUMBER_ALREADY_EXISTS         = 1243;
    const CODE_USER_MOBILE_NUMBER_ALREADY_EXISTS    = 1244;
    const CODE_WEIXIN_USER_ALREADY_EXISTS           = 1245;
    const CODE_USER_WEIXIN_USER_ALREADY_EXISTS      = 1246;
    const CODE_MOBILE_BIND_FAIL                     = 1247;
    
    const CODE_LOGIN_SUCCESS                        = 1500;
    const CODE_LOGIN_FAIL                           = 1501;
    const CODE_REACHED_MAXIMUM_ATTEMPT              = 1502;
    const CODE_LOGOUT_SUCCESS                       = 1503;
    const CODE_LOGOUT_FAIL                          = 1504;
    const CODE_WEIXIN_AUTHENTICATE_FAIL             = 1505;
    
    const CODE_PASSWORD_UPDATE_SUCCESS              = 1510;
    const CODE_PASSWORD_UPDATE_FAIL                 = 1511;
    
    const CODE_ACCESS_MENU_LIST_FAILED              = 1600;
    const CODE_ACCESS_MENU_LIST_SUCCESS             = 1601;
    
    const CODE_ADMIN_USER_GET_SUCCESS               = 2000;
    const CODE_ADMIN_USER_NOT_FOUND                 = 2001;
    const CODE_ADMIN_USER_CREATE_SUCCESS            = 2002;
    const CODE_ADMIN_ALREADY_EXISTING               = 2003;
    const CODE_ADMIN_USER_UPDATE_SUCCESS            = 2004;
    const CODE_ADMIN_USER_DELETE_SUCCESS            = 2005;

    const CODE_USER_GROUP_ADD_SUCCESS               = 2006;
    const CODE_USER_GROUP_ADD_FAIL                  = 2007;
    const CODE_USER_GROUP_GET_SUCCESS               = 2008;
    const CODE_USER_GROUP_NOT_FOUND                 = 2009;
    const CODE_USER_GROUP_DELETE_SUCCESS            = 2010;
    const CODE_USER_GROUP_DELETE_FAIL               = 2011;
    const CODE_USER_GROUP_UPDATE_SUCCESS            = 2012;
    const CODE_USER_GROUP_UPDATE_FAIL               = 2013;

    const CODE_CREATE_USER_SUCCESS                  = 2014;
    const CODE_CREATE_USER_FAIL                     = 2015;

    const CODE_UPLOAD_IMAGE_SUCCESS                 = 2021;
    const CODE_UPLOAD_IMAGE_FAIL                    = 2022;
    
    const CODE_INIT_SUCCESS                         = 10000;
    
    const CODE_ACCESS_TOKEN_ALREADY_EXISTS          = 10001;
    const CODE_ACCESS_TOKEN_ADD_SUCCESS             = 10002;
    const CODE_ACCESS_TOKEN_ADD_FAIL                = 10003;
    const CODE_ACCESS_TOKEN_GET_SUCCESS             = 10004;
    const CODE_ACCESS_TOKEN_NOT_FOUND               = 10005;
    const CODE_ACCESS_TOKEN_DELETE_SUCCESS          = 10006;
    const CODE_ACCESS_TOKEN_DELETE_FAIL             = 10007;
    const CODE_ACCESS_TOKEN_UPDATE_SUCCESS          = 10008;
    const CODE_ACCESS_TOKEN_UPDATE_FAIL             = 10009;

    const CODE_AD_BOARD_ALREADY_EXISTS          = 10021;
    const CODE_AD_BOARD_ADD_SUCCESS             = 10022;
    const CODE_AD_BOARD_ADD_FAIL                = 10023;
    const CODE_AD_BOARD_GET_SUCCESS             = 10024;
    const CODE_AD_BOARD_NOT_FOUND               = 10025;
    const CODE_AD_BOARD_DELETE_SUCCESS          = 10026;
    const CODE_AD_BOARD_DELETE_FAIL             = 10027;
    const CODE_AD_BOARD_UPDATE_SUCCESS          = 10028;
    const CODE_AD_BOARD_UPDATE_FAIL             = 10029;

    const CODE_AD_PLAN_ALREADY_EXISTS          = 10041;
    const CODE_AD_PLAN_ADD_SUCCESS             = 10042;
    const CODE_AD_PLAN_ADD_FAIL                = 10043;
    const CODE_AD_PLAN_GET_SUCCESS             = 10044;
    const CODE_AD_PLAN_NOT_FOUND               = 10045;
    const CODE_AD_PLAN_DELETE_SUCCESS          = 10046;
    const CODE_AD_PLAN_DELETE_FAIL             = 10047;
    const CODE_AD_PLAN_UPDATE_SUCCESS          = 10048;
    const CODE_AD_PLAN_UPDATE_FAIL             = 10049;

    const CODE_AD_RECORD_ALREADY_EXISTS          = 10061;
    const CODE_AD_RECORD_ADD_SUCCESS             = 10062;
    const CODE_AD_RECORD_ADD_FAIL                = 10063;
    const CODE_AD_RECORD_GET_SUCCESS             = 10064;
    const CODE_AD_RECORD_NOT_FOUND               = 10065;
    const CODE_AD_RECORD_DELETE_SUCCESS          = 10066;
    const CODE_AD_RECORD_DELETE_FAIL             = 10067;
    const CODE_AD_RECORD_UPDATE_SUCCESS          = 10068;
    const CODE_AD_RECORD_UPDATE_FAIL             = 10069;

    const CODE_AD_RECORD_CLICK_ALREADY_EXISTS          = 10081;
    const CODE_AD_RECORD_CLICK_ADD_SUCCESS             = 10082;
    const CODE_AD_RECORD_CLICK_ADD_FAIL                = 10083;
    const CODE_AD_RECORD_CLICK_GET_SUCCESS             = 10084;
    const CODE_AD_RECORD_CLICK_NOT_FOUND               = 10085;
    const CODE_AD_RECORD_CLICK_DELETE_SUCCESS          = 10086;
    const CODE_AD_RECORD_CLICK_DELETE_FAIL             = 10087;
    const CODE_AD_RECORD_CLICK_UPDATE_SUCCESS          = 10088;
    const CODE_AD_RECORD_CLICK_UPDATE_FAIL             = 10089;

    const CODE_BLACK_LIST_ALREADY_EXISTS          = 10101;
    const CODE_BLACK_LIST_ADD_SUCCESS             = 10102;
    const CODE_BLACK_LIST_ADD_FAIL                = 10103;
    const CODE_BLACK_LIST_GET_SUCCESS             = 10104;
    const CODE_BLACK_LIST_NOT_FOUND               = 10105;
    const CODE_BLACK_LIST_DELETE_SUCCESS          = 10106;
    const CODE_BLACK_LIST_DELETE_FAIL             = 10107;
    const CODE_BLACK_LIST_UPDATE_SUCCESS          = 10108;
    const CODE_BLACK_LIST_UPDATE_FAIL             = 10109;

    const CODE_BLACK_LIST_RELEASE_SUCCESS         = 10111;
    const CODE_BLACK_LIST_RELEASE_FAILED          = 10112;

    const CODE_CASHOUT_CATEGORY_ALREADY_EXISTS          = 10121;
    const CODE_CASHOUT_CATEGORY_ADD_SUCCESS             = 10122;
    const CODE_CASHOUT_CATEGORY_ADD_FAIL                = 10123;
    const CODE_CASHOUT_CATEGORY_GET_SUCCESS             = 10124;
    const CODE_CASHOUT_CATEGORY_NOT_FOUND               = 10125;
    const CODE_CASHOUT_CATEGORY_DELETE_SUCCESS          = 10126;
    const CODE_CASHOUT_CATEGORY_DELETE_FAIL             = 10127;
    const CODE_CASHOUT_CATEGORY_UPDATE_SUCCESS          = 10128;
    const CODE_CASHOUT_CATEGORY_UPDATE_FAIL             = 10129;

    const CODE_DEVICE_TOKEN_ALREADY_EXISTS          = 10141;
    const CODE_DEVICE_TOKEN_ADD_SUCCESS             = 10142;
    const CODE_DEVICE_TOKEN_ADD_FAIL                = 10143;
    const CODE_DEVICE_TOKEN_GET_SUCCESS             = 10144;
    const CODE_DEVICE_TOKEN_NOT_FOUND               = 10145;
    const CODE_DEVICE_TOKEN_DELETE_SUCCESS          = 10146;
    const CODE_DEVICE_TOKEN_DELETE_FAIL             = 10147;
    const CODE_DEVICE_TOKEN_UPDATE_SUCCESS          = 10148;
    const CODE_DEVICE_TOKEN_UPDATE_FAIL             = 10149;

    const CODE_DOMAIN_POOL_ALREADY_EXISTS          = 10161;
    const CODE_DOMAIN_POOL_ADD_SUCCESS             = 10162;
    const CODE_DOMAIN_POOL_ADD_FAIL                = 10163;
    const CODE_DOMAIN_POOL_GET_SUCCESS             = 10164;
    const CODE_DOMAIN_POOL_NOT_FOUND               = 10165;
    const CODE_DOMAIN_POOL_DELETE_SUCCESS          = 10166;
    const CODE_DOMAIN_POOL_DELETE_FAIL             = 10167;
    const CODE_DOMAIN_POOL_UPDATE_SUCCESS          = 10168;
    const CODE_DOMAIN_POOL_UPDATE_FAIL             = 10169;

    const CODE_EWALLET_ALREADY_EXISTS          = 10181;
    const CODE_EWALLET_ADD_SUCCESS             = 10182;
    const CODE_EWALLET_ADD_FAIL                = 10183;
    const CODE_EWALLET_GET_SUCCESS             = 10184;
    const CODE_EWALLET_NOT_FOUND               = 10185;
    const CODE_EWALLET_DELETE_SUCCESS          = 10186;
    const CODE_EWALLET_DELETE_FAIL             = 10187;
    const CODE_EWALLET_UPDATE_SUCCESS          = 10188;
    const CODE_EWALLET_UPDATE_FAIL             = 10189;
    const CODE_EWALLET_BALANCE_SUFFICIENT      = 10190;
    const CODE_EWALLET_BALANCE_NOT_SUFFICIENT  = 10191;



    const CODE_EWALLET_HEADER_ALREADY_EXISTS          = 10201;
    const CODE_EWALLET_HEADER_ADD_SUCCESS             = 10202;
    const CODE_EWALLET_HEADER_ADD_FAIL                = 10203;
    const CODE_EWALLET_HEADER_GET_SUCCESS             = 10204;
    const CODE_EWALLET_HEADER_NOT_FOUND               = 10205;
    const CODE_EWALLET_HEADER_DELETE_SUCCESS          = 10206;
    const CODE_EWALLET_HEADER_DELETE_FAIL             = 10207;
    const CODE_EWALLET_HEADER_UPDATE_SUCCESS          = 10208;
    const CODE_EWALLET_HEADER_UPDATE_FAIL             = 10209;

    const CODE_EWALLET_MOVEMENT_ALREADY_EXISTS          = 10221;
    const CODE_EWALLET_MOVEMENT_ADD_SUCCESS             = 10222;
    const CODE_EWALLET_MOVEMENT_ADD_FAIL                = 10223;
    const CODE_EWALLET_MOVEMENT_GET_SUCCESS             = 10224;
    const CODE_EWALLET_MOVEMENT_NOT_FOUND               = 10225;
    const CODE_EWALLET_MOVEMENT_DELETE_SUCCESS          = 10226;
    const CODE_EWALLET_MOVEMENT_DELETE_FAIL             = 10227;
    const CODE_EWALLET_MOVEMENT_UPDATE_SUCCESS          = 10228;
    const CODE_EWALLET_MOVEMENT_UPDATE_FAIL             = 10229;

    const CODE_FEEDBACK_ALREADY_EXISTS          = 10241;
    const CODE_FEEDBACK_ADD_SUCCESS             = 10242;
    const CODE_FEEDBACK_ADD_FAIL                = 10243;
    const CODE_FEEDBACK_GET_SUCCESS             = 10244;
    const CODE_FEEDBACK_NOT_FOUND               = 10245;
    const CODE_FEEDBACK_DELETE_SUCCESS          = 10246;
    const CODE_FEEDBACK_DELETE_FAIL             = 10247;
    const CODE_FEEDBACK_UPDATE_SUCCESS          = 10248;
    const CODE_FEEDBACK_UPDATE_FAIL             = 10249;

    const CODE_FUN_ALREADY_EXISTS          = 10261;
    const CODE_FUN_ADD_SUCCESS             = 10262;
    const CODE_FUN_ADD_FAIL                = 10263;
    const CODE_FUN_GET_SUCCESS             = 10264;
    const CODE_FUN_NOT_FOUND               = 10265;
    const CODE_FUN_DELETE_SUCCESS          = 10266;
    const CODE_FUN_DELETE_FAIL             = 10267;
    const CODE_FUN_UPDATE_SUCCESS          = 10268;
    const CODE_FUN_UPDATE_FAIL             = 10269;

    const CODE_LOGIN_ACCOUNT_ALREADY_EXISTS          = 10281;
    const CODE_LOGIN_ACCOUNT_ADD_SUCCESS             = 10282;
    const CODE_LOGIN_ACCOUNT_ADD_FAIL                = 10283;
    const CODE_LOGIN_ACCOUNT_GET_SUCCESS             = 10284;
    const CODE_LOGIN_ACCOUNT_NOT_FOUND               = 10285;
    const CODE_LOGIN_ACCOUNT_DELETE_SUCCESS          = 10286;
    const CODE_LOGIN_ACCOUNT_DELETE_FAIL             = 10287;
    const CODE_LOGIN_ACCOUNT_UPDATE_SUCCESS          = 10288;
    const CODE_LOGIN_ACCOUNT_UPDATE_FAIL             = 10289;

    const CODE_LOGIN_LOG_ALREADY_EXISTS          = 10301;
    const CODE_LOGIN_LOG_ADD_SUCCESS             = 10302;
    const CODE_LOGIN_LOG_ADD_FAIL                = 10303;
    const CODE_LOGIN_LOG_GET_SUCCESS             = 10304;
    const CODE_LOGIN_LOG_NOT_FOUND               = 10305;
    const CODE_LOGIN_LOG_DELETE_SUCCESS          = 10306;
    const CODE_LOGIN_LOG_DELETE_FAIL             = 10307;
    const CODE_LOGIN_LOG_UPDATE_SUCCESS          = 10308;
    const CODE_LOGIN_LOG_UPDATE_FAIL             = 10309;

    const CODE_NEWS_ALREADY_EXISTS          = 10321;
    const CODE_NEWS_ADD_SUCCESS             = 10322;
    const CODE_NEWS_ADD_FAIL                = 10323;
    const CODE_NEWS_GET_SUCCESS             = 10324;
    const CODE_NEWS_NOT_FOUND               = 10325;
    const CODE_NEWS_DELETE_SUCCESS          = 10326;
    const CODE_NEWS_DELETE_FAIL             = 10327;
    const CODE_NEWS_UPDATE_SUCCESS          = 10328;
    const CODE_NEWS_UPDATE_FAIL             = 10329;

    const CODE_NEWS_CATEGORY_ALREADY_EXISTS          = 10341;
    const CODE_NEWS_CATEGORY_ADD_SUCCESS             = 10342;
    const CODE_NEWS_CATEGORY_ADD_FAIL                = 10343;
    const CODE_NEWS_CATEGORY_GET_SUCCESS             = 10344;
    const CODE_NEWS_CATEGORY_NOT_FOUND               = 10345;
    const CODE_NEWS_CATEGORY_DELETE_SUCCESS          = 10346;
    const CODE_NEWS_CATEGORY_DELETE_FAIL             = 10347;
    const CODE_NEWS_CATEGORY_UPDATE_SUCCESS          = 10348;
    const CODE_NEWS_CATEGORY_UPDATE_FAIL             = 10349;

    const CODE_NEWS_DETAIL_ALREADY_EXISTS          = 10361;
    const CODE_NEWS_DETAIL_ADD_SUCCESS             = 10362;
    const CODE_NEWS_DETAIL_ADD_FAIL                = 10363;
    const CODE_NEWS_DETAIL_GET_SUCCESS             = 10364;
    const CODE_NEWS_DETAIL_NOT_FOUND               = 10365;
    const CODE_NEWS_DETAIL_DELETE_SUCCESS          = 10366;
    const CODE_NEWS_DETAIL_DELETE_FAIL             = 10367;
    const CODE_NEWS_DETAIL_UPDATE_SUCCESS          = 10368;
    const CODE_NEWS_DETAIL_UPDATE_FAIL             = 10369;

    const CODE_NEWS_PICS_ALREADY_EXISTS          = 10381;
    const CODE_NEWS_PICS_ADD_SUCCESS             = 10382;
    const CODE_NEWS_PICS_ADD_FAIL                = 10383;
    const CODE_NEWS_PICS_GET_SUCCESS             = 10384;
    const CODE_NEWS_PICS_NOT_FOUND               = 10385;
    const CODE_NEWS_PICS_DELETE_SUCCESS          = 10386;
    const CODE_NEWS_PICS_DELETE_FAIL             = 10387;
    const CODE_NEWS_PICS_UPDATE_SUCCESS          = 10388;
    const CODE_NEWS_PICS_UPDATE_FAIL             = 10389;

    const CODE_NEWS_SHARE_ALREADY_EXISTS          = 10401;
    const CODE_NEWS_SHARE_ADD_SUCCESS             = 10402;
    const CODE_NEWS_SHARE_ADD_FAIL                = 10403;
    const CODE_NEWS_SHARE_GET_SUCCESS             = 10404;
    const CODE_NEWS_SHARE_NOT_FOUND               = 10405;
    const CODE_NEWS_SHARE_DELETE_SUCCESS          = 10406;
    const CODE_NEWS_SHARE_DELETE_FAIL             = 10407;
    const CODE_NEWS_SHARE_UPDATE_SUCCESS          = 10408;
    const CODE_NEWS_SHARE_UPDATE_FAIL             = 10409;
    const CODE_NEWS_SHARE_GENERATE_FAIL           = 10410;
    const CODE_NEWS_SHARE_GENERATE_SUCCESS        = 10411;
    const CODE_NEWS_SHARE_BACK_FAIL               = 10412;
    const CODE_NEWS_SHARE_BACK_SUCCESS            = 10413;

//    const CODE_NEWS_STATICS_ARELADY_EXISTS          = 10411;
//    const CODE_NEWS_STATICS_ADD_SUCCESS             = 10412;
//    const CODE_NEWS_STATICS_ADD_FAIL                = 10413;
//    const CODE_NEWS_STATICS_GET_SUCCESS             = 10414;
//    const CODE_NEWS_STATICS_NOT_FOUND               = 10415;
//    const CODE_NEWS_STATICS_DELETE_SUCCESS          = 10416;
//    const CODE_NEWS_STATICS_DELETE_FAIL             = 10417;
//    const CODE_NEWS_STATICS_UPDATE_SUCCESS          = 10418;
//    const CODE_NEWS_STATICS_UPDATE_FAIL             = 10419;

    const CODE_NEWS_STATICS_LIST_ALREADY_EXISTS          = 10421;
    const CODE_NEWS_STATICS_LIST_ADD_SUCCESS             = 10422;
    const CODE_NEWS_STATICS_LIST_ADD_FAIL                = 10423;
    const CODE_NEWS_STATICS_LIST_GET_SUCCESS             = 10424;
    const CODE_NEWS_STATICS_LIST_NOT_FOUND               = 10425;
    const CODE_NEWS_STATICS_LIST_DELETE_SUCCESS          = 10426;
    const CODE_NEWS_STATICS_LIST_DELETE_FAIL             = 10427;
    const CODE_NEWS_STATICS_LIST_UPDATE_SUCCESS          = 10428;
    const CODE_NEWS_STATICS_LIST_UPDATE_FAIL             = 10429;

    const CODE_NEWS_VISIT_HISTORY_ALREADY_EXISTS          = 10441;
    const CODE_NEWS_VISIT_HISTORY_ADD_SUCCESS             = 10442;
    const CODE_NEWS_VISIT_HISTORY_ADD_FAIL                = 10443;
    const CODE_NEWS_VISIT_HISTORY_GET_SUCCESS             = 10444;
    const CODE_NEWS_VISIT_HISTORY_NOT_FOUND               = 10445;
    const CODE_NEWS_VISIT_HISTORY_DELETE_SUCCESS          = 10446;
    const CODE_NEWS_VISIT_HISTORY_DELETE_FAIL             = 10447;
    const CODE_NEWS_VISIT_HISTORY_UPDATE_SUCCESS          = 10448;
    const CODE_NEWS_VISIT_HISTORY_UPDATE_FAIL             = 10449;

    const CODE_OTP_ALREADY_EXISTS          = 10461;
    const CODE_OTP_ADD_SUCCESS             = 10462;
    const CODE_OTP_ADD_FAIL                = 10463;
    const CODE_OTP_GET_SUCCESS             = 10464;
    const CODE_OTP_NOT_FOUND               = 10465;
    const CODE_OTP_DELETE_SUCCESS          = 10466;
    const CODE_OTP_DELETE_FAIL             = 10467;
    const CODE_OTP_UPDATE_SUCCESS          = 10468;
    const CODE_OTP_UPDATE_FAIL             = 10469;

    const CODE_PASSWORD_LOG_ALREADY_EXISTS          = 10481;
    const CODE_PASSWORD_LOG_ADD_SUCCESS             = 10482;
    const CODE_PASSWORD_LOG_ADD_FAIL                = 10483;
    const CODE_PASSWORD_LOG_GET_SUCCESS             = 10484;
    const CODE_PASSWORD_LOG_NOT_FOUND               = 10485;
    const CODE_PASSWORD_LOG_DELETE_SUCCESS          = 10486;
    const CODE_PASSWORD_LOG_DELETE_FAIL             = 10487;
    const CODE_PASSWORD_LOG_UPDATE_SUCCESS          = 10488;
    const CODE_PASSWORD_LOG_UPDATE_FAIL             = 10489;

    

    const CODE_USER_FUN_ALREADY_EXISTS          = 10521;
    const CODE_USER_FUN_ADD_SUCCESS             = 10522;
    const CODE_USER_FUN_ADD_FAIL                = 10523;
    const CODE_USER_FUN_GET_SUCCESS             = 10524;
    const CODE_USER_FUN_NOT_FOUND               = 10525;
    const CODE_USER_FUN_DELETE_SUCCESS          = 10526;
    const CODE_USER_FUN_DELETE_FAIL             = 10527;
    const CODE_USER_FUN_UPDATE_SUCCESS          = 10528;
    const CODE_USER_FUN_UPDATE_FAIL             = 10529;

    const CODE_USER_AD_BOARD_ALREADY_EXISTS          = 10541;
    const CODE_USER_AD_BOARD_ADD_SUCCESS             = 10542;
    const CODE_USER_AD_BOARD_ADD_FAIL                = 10543;
    const CODE_USER_AD_BOARD_GET_SUCCESS             = 10544;
    const CODE_USER_AD_BOARD_NOT_FOUND               = 10545;
    const CODE_USER_AD_BOARD_DELETE_SUCCESS          = 10546;
    const CODE_USER_AD_BOARD_DELETE_FAIL             = 10547;
    const CODE_USER_AD_BOARD_UPDATE_SUCCESS          = 10548;
    const CODE_USER_AD_BOARD_UPDATE_FAIL             = 10549;

    const CODE_USER_AD_MATE_ALREADY_EXISTS          = 10561;
    const CODE_USER_AD_MATE_ADD_SUCCESS             = 10562;
    const CODE_USER_AD_MATE_ADD_FAIL                = 10563;
    const CODE_USER_AD_MATE_GET_SUCCESS             = 10564;
    const CODE_USER_AD_MATE_NOT_FOUND               = 10565;
    const CODE_USER_AD_MATE_DELETE_SUCCESS          = 10566;
    const CODE_USER_AD_MATE_DELETE_FAIL             = 10567;
    const CODE_USER_AD_MATE_UPDATE_SUCCESS          = 10568;
    const CODE_USER_AD_MATE_UPDATE_FAIL             = 10569;

    const CODE_USER_CASHOUT_MODE_ALREADY_EXISTS          = 10581;
    const CODE_USER_CASHOUT_MODE_ADD_SUCCESS             = 10582;
    const CODE_USER_CASHOUT_MODE_ADD_FAIL                = 10583;
    const CODE_USER_CASHOUT_MODE_GET_SUCCESS             = 10584;
    const CODE_USER_CASHOUT_MODE_NOT_FOUND               = 10585;
    const CODE_USER_CASHOUT_MODE_DELETE_SUCCESS          = 10586;
    const CODE_USER_CASHOUT_MODE_DELETE_FAIL             = 10587;
    const CODE_USER_CASHOUT_MODE_UPDATE_SUCCESS          = 10588;
    const CODE_USER_CASHOUT_MODE_UPDATE_FAIL             = 10589;

    
    const CODE_CASHOUT_REQUEST_ALREADY_EXISTS          = 10621;
    const CODE_CASHOUT_REQUEST_ADD_SUCCESS             = 10622;
    const CODE_CASHOUT_REQUEST_ADD_FAIL                = 10623;
    const CODE_CASHOUT_REQUEST_GET_SUCCESS             = 10624;
    const CODE_CASHOUT_REQUEST_NOT_FOUND               = 10625;
    const CODE_CASHOUT_REQUEST_DELETE_SUCCESS          = 10626;
    const CODE_CASHOUT_REQUEST_DELETE_FAIL             = 10627;
    const CODE_CASHOUT_REQUEST_UPDATE_SUCCESS          = 10628;
    const CODE_CASHOUT_REQUEST_UPDATE_FAIL             = 10629;
    const CODE_CASHOUT_REQUEST_TIME_EXCEEDED           = 10630;
    const CODE_CASHOUT_ONCE_A_DAY                      = 10631;
    const CODE_CASHOUT_SUCCESS                         = 10632;
    const CODE_CASHOUT_FAIL                            = 10633;
    const CODE_INVALID_CASHOUT_REQUEST_STATUS          = 10634;
    const CODE_INVALID_USER_STATUS                     = 10635;
    const CODE_WORK_DAY_CAN_CASHOUT                    = 10636;


    const CODE_USER_CHILD_ALREADY_EXISTS          = 10641;
    const CODE_USER_CHILD_ADD_SUCCESS             = 10642;
    const CODE_USER_CHILD_ADD_FAIL                = 10643;
    const CODE_USER_CHILD_GET_SUCCESS             = 10644;
    const CODE_USER_CHILD_NOT_FOUND               = 10645;
    const CODE_USER_CHILD_DELETE_SUCCESS          = 10646;
    const CODE_USER_CHILD_DELETE_FAIL             = 10647;
    const CODE_USER_CHILD_UPDATE_SUCCESS          = 10648;
    const CODE_USER_CHILD_UPDATE_FAIL             = 10649;

    const CODE_USER_EXTEND_ALREADY_EXISTS          = 10661;
    const CODE_USER_EXTEND_ADD_SUCCESS             = 10662;
    const CODE_USER_EXTEND_ADD_FAIL                = 10663;
    const CODE_USER_EXTEND_GET_SUCCESS             = 10664;
    const CODE_USER_EXTEND_NOT_FOUND               = 10665;
    const CODE_USER_EXTEND_DELETE_SUCCESS          = 10666;
    const CODE_USER_EXTEND_DELETE_FAIL             = 10667;
    const CODE_USER_EXTEND_UPDATE_SUCCESS          = 10668;
    const CODE_USER_EXTEND_UPDATE_FAIL             = 10669;

    const CODE_USER_INVITE_ALREADY_EXISTS          = 10681;
    const CODE_USER_INVITE_ADD_SUCCESS             = 10682;
    const CODE_USER_INVITE_ADD_FAIL                = 10683;
    const CODE_USER_INVITE_GET_SUCCESS             = 10684;
    const CODE_USER_INVITE_NOT_FOUND               = 10685;
    const CODE_USER_INVITE_DELETE_SUCCESS          = 10686;
    const CODE_USER_INVITE_DELETE_FAIL             = 10687;
    const CODE_USER_INVITE_UPDATE_SUCCESS          = 10688;
    const CODE_USER_INVITE_UPDATE_FAIL             = 10689;
    const CODE_USER_ALREADY_HAS_MASTER             = 10690;
    const CODE_USER_HAS_FRIEND_CANNOT_BIND         = 10691;

    const CODE_USER_MESSAGE_ALREADY_EXISTS          = 10741;
    const CODE_USER_MESSAGE_ADD_SUCCESS             = 10742;
    const CODE_USER_MESSAGE_ADD_FAIL                = 10743;
    const CODE_USER_MESSAGE_GET_SUCCESS             = 10744;
    const CODE_USER_MESSAGE_NOT_FOUND               = 10745;
    const CODE_USER_MESSAGE_DELETE_SUCCESS          = 10746;
    const CODE_USER_MESSAGE_DELETE_FAIL             = 10747;
    const CODE_USER_MESSAGE_UPDATE_SUCCESS          = 10748;
    const CODE_USER_MESSAGE_UPDATE_FAIL             = 10749;

    const CODE_USER_PROFILE_ALREADY_EXISTS          = 10761;
    const CODE_USER_PROFILE_ADD_SUCCESS             = 10762;
    const CODE_USER_PROFILE_ADD_FAIL                = 10763;
    const CODE_USER_PROFILE_GET_SUCCESS             = 10764;
    const CODE_USER_PROFILE_NOT_FOUND               = 10765;
    const CODE_USER_PROFILE_DELETE_SUCCESS          = 10766;
    const CODE_USER_PROFILE_DELETE_FAIL             = 10767;
    const CODE_USER_PROFILE_UPDATE_SUCCESS          = 10768;
    const CODE_USER_PROFILE_UPDATE_FAIL             = 10769;

    const CODE_USER_RELATION_ALREADY_EXISTS          = 10781;
    const CODE_USER_RELATION_ADD_SUCCESS             = 10782;
    const CODE_USER_RELATION_ADD_FAIL                = 10783;
    const CODE_USER_RELATION_GET_SUCCESS             = 10784;
    const CODE_USER_RELATION_NOT_FOUND               = 10785;
    const CODE_USER_RELATION_DELETE_SUCCESS          = 10786;
    const CODE_USER_RELATION_DELETE_FAIL             = 10787;
    const CODE_USER_RELATION_UPDATE_SUCCESS          = 10788;
    const CODE_USER_RELATION_UPDATE_FAIL             = 10789;
    const CODE_USER_MASTER_NOT_BIND                  = 10790;

    
    const CODE_VERSION_CONTROL_ALREADY_EXISTS          = 10841;
    const CODE_VERSION_CONTROL_ADD_SUCCESS             = 10842;
    const CODE_VERSION_CONTROL_ADD_FAIL                = 10843;
    const CODE_VERSION_CONTROL_GET_SUCCESS             = 10844;
    const CODE_VERSION_CONTROL_NOT_FOUND               = 10845;
    const CODE_VERSION_CONTROL_DELETE_SUCCESS          = 10846;
    const CODE_VERSION_CONTROL_DELETE_FAIL             = 10847;
    const CODE_VERSION_CONTROL_UPDATE_SUCCESS          = 10848;
    const CODE_VERSION_CONTROL_UPDATE_FAIL             = 10849;

    const CODE_WXCONFIG_ALREADY_EXISTS          = 10861;
    const CODE_WXCONFIG_ADD_SUCCESS             = 10862;
    const CODE_WXCONFIG_ADD_FAIL                = 10863;
    const CODE_WXCONFIG_GET_SUCCESS             = 10864;
    const CODE_WXCONFIG_NOT_FOUND               = 10865;
    const CODE_WXCONFIG_DELETE_SUCCESS          = 10866;
    const CODE_WXCONFIG_DELETE_FAIL             = 10867;
    const CODE_WXCONFIG_UPDATE_SUCCESS          = 10868;
    const CODE_WXCONFIG_UPDATE_FAIL             = 10869;

    const CODE_WXUSER_ARELADY_EXISTS          = 10881;
    const CODE_WXUSER_ADD_SUCCESS             = 10882;
    const CODE_WXUSER_ADD_FAIL                = 10883;
    const CODE_WXUSER_GET_SUCCESS             = 10884;
    const CODE_WXUSER_NOT_FOUND               = 10885;
    const CODE_WXUSER_DELETE_SUCCESS          = 10886;
    const CODE_WXUSER_DELETE_FAIL             = 10887;
    const CODE_WXUSER_UPDATE_SUCCESS          = 10888;
    const CODE_WXUSER_UPDATE_FAIL             = 10889;

    
    const CODE_MISSION_ARELADY_EXISTS          = 10901;
    const CODE_MISSION_ADD_SUCCESS             = 10902;
    const CODE_MISSION_ADD_FAIL                = 10903;
    const CODE_MISSION_GET_SUCCESS             = 10904;
    const CODE_MISSION_NOT_FOUND               = 10905;
    const CODE_MISSION_DELETE_SUCCESS          = 10906;
    const CODE_MISSION_DELETE_FAIL             = 10907;
    const CODE_MISSION_UPDATE_SUCCESS          = 10908;
    const CODE_MISSION_UPDATE_FAIL             = 10909;

    const CODE_USER_MISSION_ARELADY_EXISTS          = 10921;
    const CODE_USER_MISSION_ADD_SUCCESS             = 10922;
    const CODE_USER_MISSION_ADD_FAIL                = 10923;
    const CODE_USER_MISSION_GET_SUCCESS             = 10924;
    const CODE_USER_MISSION_NOT_FOUND               = 10925;
    const CODE_USER_MISSION_DELETE_SUCCESS          = 10926;
    const CODE_USER_MISSION_DELETE_FAIL             = 10927;
    const CODE_USER_MISSION_UPDATE_SUCCESS          = 10928;
    const CODE_USER_MISSION_UPDATE_FAIL             = 10929;
    
    const CODE_NEWS_DOMAIN_POOL_ARELADY_EXISTS          = 10941;
    const CODE_NEWS_DOMAIN_POOL_ADD_SUCCESS             = 10942;
    const CODE_NEWS_DOMAIN_POOL_ADD_FAIL                = 10943;
    const CODE_NEWS_DOMAIN_POOL_GET_SUCCESS             = 10944;
    const CODE_NEWS_DOMAIN_POOL_NOT_FOUND               = 10945;
    const CODE_NEWS_DOMAIN_POOL_DELETE_SUCCESS          = 10946;
    const CODE_NEWS_DOMAIN_POOL_DELETE_FAIL             = 10947;
    const CODE_NEWS_DOMAIN_POOL_UPDATE_SUCCESS          = 10948;
    const CODE_NEWS_DOMAIN_POOL_UPDATE_FAIL             = 10949;

    const CODE_CORE_CONFIG_DATA_ARELADY_EXISTS          = 10961;
    const CODE_CORE_CONFIG_DATA_ADD_SUCCESS             = 10962;
    const CODE_CORE_CONFIG_DATA_ADD_FAIL                = 10963;
    const CODE_CORE_CONFIG_DATA_GET_SUCCESS             = 10964;
    const CODE_CORE_CONFIG_DATA_NOT_FOUND               = 10965;
    const CODE_CORE_CONFIG_DATA_DELETE_SUCCESS          = 10966;
    const CODE_CORE_CONFIG_DATA_DELETE_FAIL             = 10967;
    const CODE_CORE_CONFIG_DATA_UPDATE_SUCCESS          = 10968;
    const CODE_CORE_CONFIG_DATA_UPDATE_FAIL             = 10969;

    const CODE_APP_DOMAIN_POOL_ARELADY_EXISTS          = 10981;
    const CODE_APP_DOMAIN_POOL_ADD_SUCCESS             = 10982;
    const CODE_APP_DOMAIN_POOL_ADD_FAIL                = 10983;
    const CODE_APP_DOMAIN_POOL_GET_SUCCESS             = 10984;
    const CODE_APP_DOMAIN_POOL_NOT_FOUND               = 10985;
    const CODE_APP_DOMAIN_POOL_DELETE_SUCCESS          = 10986;
    const CODE_APP_DOMAIN_POOL_DELETE_FAIL             = 10987;
    const CODE_APP_DOMAIN_POOL_UPDATE_SUCCESS          = 10988;
    const CODE_APP_DOMAIN_POOL_UPDATE_FAIL             = 10989;


    //收徒提现奖励
    const CODE_CASHOUT_PROFIT_CONFIG_ADD_SUCCESS        = 10990;
    const CODE_CASHOUT_PROFIT_CONFIG_ADD_FAIL           = 10991;
    const CODE_CASHOUT_PROFIT_CONFIG_UPDATE_SUCCESS     = 10992;
    const CODE_CASHOUT_PROFIT_CONFIG_UPDATE_FAIL        = 10993;
    const CODE_CASHOUT_PROFIT_CONFIG_DELETE_SUCCESS     = 10994;
    const CODE_CASHOUT_PROFIT_CONFIG_DELETE_FAIL        = 10995;
    const CODE_CASHOUT_PROFIT_CONFIG_GET_SUCCESS        = 10996;
    const CODE_CASHOUT_PROFIT_CONFIG_NOT_FOUND          = 10997;


    //分享海报
    const CODE_USER_SHARE_POSTER_ADD_SUCCESS        = 10998;
    const CODE_USER_SHARE_POSTER_ADD_FAIL           = 10999;
    const CODE_USER_SHARE_POSTER_UPDATE_SUCCESS     = 11000;
    const CODE_USER_SHARE_POSTER_UPDATE_FAIL        = 11001;
    const CODE_USER_SHARE_POSTER_DELETE_SUCCESS     = 11002;
    const CODE_USER_SHARE_POSTER_DELETE_FAIL        = 11003;
    const CODE_USER_SHARE_POSTER_GET_SUCCESS        = 11004;
    const CODE_USER_SHARE_POSTER_NOT_FOUND          = 11005;
    

    

    const CODE_USER_HOUR_STATICS_ARELADY_EXISTS          = 11412;
    const CODE_USER_HOUR_STATICS_ADD_SUCCESS             = 11413;
    const CODE_USER_HOUR_STATICS_ADD_FAIL                = 11414;
    const CODE_USER_HOUR_STATICS_GET_SUCCESS             = 11415;
    const CODE_USER_HOUR_STATICS_NOT_FOUND               = 11416;
    const CODE_USER_HOUR_STATICS_DELETE_SUCCESS          = 11417;
    const CODE_USER_HOUR_STATICS_DELETE_FAIL             = 11418;
    const CODE_USER_HOUR_STATICS_UPDATE_SUCCESS          = 11419;
    const CODE_USER_HOUR_STATICS_UPDATE_FAIL             = 11420;
    
    const CODE_USER_STATICS_ARELADY_EXISTS          = 11421;
    const CODE_USER_STATICS_ADD_SUCCESS             = 11422;
    const CODE_USER_STATICS_ADD_FAIL                = 11423;
    const CODE_USER_STATICS_GET_SUCCESS             = 11424;
    const CODE_USER_STATICS_NOT_FOUND               = 11425;
    const CODE_USER_STATICS_DELETE_SUCCESS          = 11426;
    const CODE_USER_STATICS_DELETE_FAIL             = 11427;
    const CODE_USER_STATICS_UPDATE_SUCCESS          = 11428;
    const CODE_USER_STATICS_UPDATE_FAIL             = 11429;

    const CODE_USER_DAILY_STATICS_ARELADY_EXISTS          = 11441;
    const CODE_USER_DAILY_STATICS_ADD_SUCCESS             = 11442;
    const CODE_USER_DAILY_STATICS_ADD_FAIL                = 11443;
    const CODE_USER_DAILY_STATICS_GET_SUCCESS             = 11444;
    const CODE_USER_DAILY_STATICS_NOT_FOUND               = 11445;
    const CODE_USER_DAILY_STATICS_DELETE_SUCCESS          = 11446;
    const CODE_USER_DAILY_STATICS_DELETE_FAIL             = 11447;
    const CODE_USER_DAILY_STATICS_UPDATE_SUCCESS          = 11448;
    const CODE_USER_DAILY_STATICS_UPDATE_FAIL             = 11449;



    const CODE_NEWS_PRICE_CONFIG_NOT_FOUND                = 11450;
    const CODE_NEWS_PRICE_CONFIG_GET_SUCCESS              = 11451;
    const CODE_NEWS_PRICE_CONFIG_ADD_SUCCESS              = 11452;
    const CODE_NEWS_PRICE_CONFIG_ADD_FAIL                 = 11453;
    const CODE_NEWS_PRICE_CONFIG_DELETE_SUCCESS           = 11454;
    const CODE_NEWS_PRICE_CONFIG_DELETE_FAIL              = 11455;
    const CODE_NEWS_PRICE_CONFIG_UPDATE_SUCCESS           = 11456;
    const CODE_NEWS_PRICE_CONFIG_UPDATE_FAIL              = 11457;



    const CODE_NEWS_VISIT_TODAY_NOT_FOUND                = 11458;
    const CODE_NEWS_VISIT_TODAY_GET_SUCCESS              = 11459;
    const CODE_NEWS_VISIT_TODAY_ADD_SUCCESS              = 11460;
    const CODE_NEWS_VISIT_TODAY_ADD_FAIL                 = 11461;
    const CODE_NEWS_VISIT_TODAY_DELETE_SUCCESS           = 11462;
    const CODE_NEWS_VISIT_TODAY_DELETE_FAIL              = 11463;
    const CODE_NEWS_VISIT_TODAY_UPDATE_SUCCESS           = 11464;
    const CODE_NEWS_VISIT_TODAY_UPDATE_FAIL              = 11465;


    const CODE_NEWS_VISIT_MONTH_NOT_FOUND                = 11466;
    const CODE_NEWS_VISIT_MONTH_GET_SUCCESS              = 11467;
    const CODE_NEWS_VISIT_MONTH_ADD_SUCCESS              = 11468;
    const CODE_NEWS_VISIT_MONTH_ADD_FAIL                 = 11469;
    const CODE_NEWS_VISIT_MONTH_DELETE_SUCCESS           = 11470;
    const CODE_NEWS_VISIT_MONTH_DELETE_FAIL              = 11471;
    const CODE_NEWS_VISIT_MONTH_UPDATE_SUCCESS           = 11472;
    const CODE_NEWS_VISIT_MONTH_UPDATE_FAIL              = 11473;


    const CODE_NEWS_STATICS_ARELADY_EXISTS          = 11474;
    const CODE_NEWS_STATICS_ADD_SUCCESS             = 11475;
    const CODE_NEWS_STATICS_ADD_FAIL                = 11476;
    const CODE_NEWS_STATICS_GET_SUCCESS             = 11477;
    const CODE_NEWS_STATICS_NOT_FOUND               = 11478;
    const CODE_NEWS_STATICS_DELETE_SUCCESS          = 11479;
    const CODE_NEWS_STATICS_DELETE_FAIL             = 11480;
    const CODE_NEWS_STATICS_UPDATE_SUCCESS          = 11481;
    const CODE_NEWS_STATICS_UPDATE_FAIL             = 11482;


    const CODE_NEWS_DAILY_STATICS_ARELADY_EXISTS          = 11483;
    const CODE_NEWS_DAILY_STATICS_ADD_SUCCESS             = 11484;
    const CODE_NEWS_DAILY_STATICS_ADD_FAIL                = 11485;
    const CODE_NEWS_DAILY_STATICS_GET_SUCCESS             = 11486;
    const CODE_NEWS_DAILY_STATICS_NOT_FOUND               = 11487;
    const CODE_NEWS_DAILY_STATICS_DELETE_SUCCESS          = 11488;
    const CODE_NEWS_DAILY_STATICS_DELETE_FAIL             = 11489;
    const CODE_NEWS_DAILY_STATICS_UPDATE_SUCCESS          = 11490;
    const CODE_NEWS_DAILY_STATICS_UPDATE_FAIL             = 11491;


    const CODE_PLATFORM_STATICS_ARELADY_EXISTS          = 11491;
    const CODE_PLATFORM_STATICS_ADD_SUCCESS             = 11493;
    const CODE_PLATFORM_STATICS_ADD_FAIL                = 11494;
    const CODE_PLATFORM_STATICS_GET_SUCCESS             = 11495;
    const CODE_PLATFORM_STATICS_NOT_FOUND               = 11496;
    const CODE_PLATFORM_STATICS_DELETE_SUCCESS          = 11497;
    const CODE_PLATFORM_STATICS_DELETE_FAIL             = 11498;
    const CODE_PLATFORM_STATICS_UPDATE_SUCCESS          = 11499;
    const CODE_PLATFORM_STATICS_UPDATE_FAIL             = 11500;


    const CODE_PLATFORM_DAILY_STATICS_ARELADY_EXISTS          = 11501;
    const CODE_PLATFORM_DAILY_STATICS_ADD_SUCCESS             = 11502;
    const CODE_PLATFORM_DAILY_STATICS_ADD_FAIL                = 11503;
    const CODE_PLATFORM_DAILY_STATICS_GET_SUCCESS             = 11504;
    const CODE_PLATFORM_DAILY_STATICS_NOT_FOUND               = 11505;
    const CODE_PLATFORM_DAILY_STATICS_DELETE_SUCCESS          = 11506;
    const CODE_PLATFORM_DAILY_STATICS_DELETE_FAIL             = 11507;
    const CODE_PLATFORM_DAILY_STATICS_UPDATE_SUCCESS          = 11508;
    const CODE_PLATFORM_DAILY_STATICS_UPDATE_FAIL             = 11509;



    const CODE_PLATFORM_HOUR_STATICS_ARELADY_EXISTS          = 11510;
    const CODE_PLATFORM_HOUR_STATICS_ADD_SUCCESS             = 11511;
    const CODE_PLATFORM_HOUR_STATICS_ADD_FAIL                = 11512;
    const CODE_PLATFORM_HOUR_STATICS_GET_SUCCESS             = 11513;
    const CODE_PLATFORM_HOUR_STATICS_NOT_FOUND               = 11514;
    const CODE_PLATFORM_HOUR_STATICS_DELETE_SUCCESS          = 11515;
    const CODE_PLATFORM_HOUR_STATICS_DELETE_FAIL             = 11516;
    const CODE_PLATFORM_HOUR_STATICS_UPDATE_SUCCESS          = 11517;
    const CODE_PLATFORM_HOUR_STATICS_UPDATE_FAIL             = 11518;

    const CODE_DEDU_CONFIG_ADD_SUCCESS                       = 11519;
    const CODE_DEDU_CONFIG_ADD_FAIL                          = 11520;
    const CODE_DEDU_CONFIG_GET_SUCCESS                       = 11521;
    const CODE_DEDU_CONFIG_NOT_FOUND                         = 11522;
    const CODE_DEDU_CONFIG_DELETE_SUCCESS                    = 11523;
    const CODE_DEDU_CONFIG_DELETE_FAIL                       = 11524;
    const CODE_DEDU_CONFIG_UPDATE_SUCCESS                    = 11525;
    const CODE_DEDU_CONFIG_UPDATE_FAIL                       = 11526;


    const CODE_MATE_VISIT_TODAY_ADD_SUCCESS                       = 11527;
    const CODE_MATE_VISIT_TODAY_ADD_FAIL                          = 11528;
    const CODE_MATE_VISIT_TODAY_GET_SUCCESS                       = 11529;
    const CODE_MATE_VISIT_TODAY_NOT_FOUND                         = 11530;
    const CODE_MATE_VISIT_TODAY_DELETE_SUCCESS                    = 11531;
    const CODE_MATE_VISIT_TODAY_DELETE_FAIL                       = 11532;
    const CODE_MATE_VISIT_TODAY_UPDATE_SUCCESS                    = 11533;
    const CODE_MATE_VISIT_TODAY_UPDATE_FAIL                       = 11534;

    const CODE_MATE_VISIT_MONTH_ADD_SUCCESS                       = 11535;
    const CODE_MATE_VISIT_MONTH_ADD_FAIL                          = 11536;
    const CODE_MATE_VISIT_MONTH_GET_SUCCESS                       = 11537;
    const CODE_MATE_VISIT_MONTH_NOT_FOUND                         = 11538;
    const CODE_MATE_VISIT_MONTH_DELETE_SUCCESS                    = 11539;
    const CODE_MATE_VISIT_MONTH_DELETE_FAIL                       = 11540;
    const CODE_MATE_VISIT_MONTH_UPDATE_SUCCESS                    = 11541;
    const CODE_MATE_VISIT_MONTH_UPDATE_FAIL                       = 11542;

    const CODE_AD_STATICS_ADD_SUCCESS                       = 11543;
    const CODE_AD_STATICS_ADD_FAIL                          = 11544;
    const CODE_AD_STATICS_GET_SUCCESS                       = 11545;
    const CODE_AD_STATICS_NOT_FOUND                         = 11546;
    const CODE_AD_STATICS_DELETE_SUCCESS                    = 11547;
    const CODE_AD_STATICS_DELETE_FAIL                       = 11548;
    const CODE_AD_STATICS_UPDATE_SUCCESS                    = 11549;
    const CODE_AD_STATICS_UPDATE_FAIL                       = 11550;

    const CODE_AD_DAILY_STATICS_ADD_SUCCESS                       = 11551;
    const CODE_AD_DAILY_STATICS_ADD_FAIL                          = 11552;
    const CODE_AD_DAILY_STATICS_GET_SUCCESS                       = 11553;
    const CODE_AD_DAILY_STATICS_NOT_FOUND                         = 11554;
    const CODE_AD_DAILY_STATICS_DELETE_SUCCESS                    = 11555;
    const CODE_AD_DAILY_STATICS_DELETE_FAIL                       = 11556;
    const CODE_AD_DAILY_STATICS_UPDATE_SUCCESS                    = 11557;
    const CODE_AD_DAILY_STATICS_UPDATE_FAIL                       = 11558;


    const CODE_AD_HOUR_STATICS_ADD_SUCCESS                       = 11559;
    const CODE_AD_HOUR_STATICS_ADD_FAIL                          = 11560;
    const CODE_AD_HOUR_STATICS_GET_SUCCESS                       = 11561;
    const CODE_AD_HOUR_STATICS_NOT_FOUND                         = 11562;
    const CODE_AD_HOUR_STATICS_DELETE_SUCCESS                    = 11563;
    const CODE_AD_HOUR_STATICS_DELETE_FAIL                       = 11564;
    const CODE_AD_HOUR_STATICS_UPDATE_SUCCESS                    = 11565;
    const CODE_AD_HOUR_STATICS_UPDATE_FAIL                       = 11566;


    const CODE_AD_MATE_STATICS_ADD_SUCCESS                       = 11567;
    const CODE_AD_MATE_STATICS_ADD_FAIL                          = 11568;
    const CODE_AD_MATE_STATICS_GET_SUCCESS                       = 11569;
    const CODE_AD_MATE_STATICS_NOT_FOUND                         = 11570;
    const CODE_AD_MATE_STATICS_DELETE_SUCCESS                    = 11571;
    const CODE_AD_MATE_STATICS_DELETE_FAIL                       = 11572;
    const CODE_AD_MATE_STATICS_UPDATE_SUCCESS                    = 11573;
    const CODE_AD_MATE_STATICS_UPDATE_FAIL                       = 11574;


    const CODE_AD_MATE_DAILY_STATICS_ADD_SUCCESS                       = 11575;
    const CODE_AD_MATE_DAILY_STATICS_ADD_FAIL                          = 11576;
    const CODE_AD_MATE_DAILY_STATICS_GET_SUCCESS                       = 11577;
    const CODE_AD_MATE_DAILY_STATICS_NOT_FOUND                         = 11578;
    const CODE_AD_MATE_DAILY_STATICS_DELETE_SUCCESS                    = 11579;
    const CODE_AD_MATE_DAILY_STATICS_DELETE_FAIL                       = 11580;
    const CODE_AD_MATE_DAILY_STATICS_UPDATE_SUCCESS                    = 11581;
    const CODE_AD_MATE_DAILY_STATICS_UPDATE_FAIL                       = 11582;


    const CODE_AD_MATE_HOUR_STATICS_ADD_SUCCESS                       = 11583;
    const CODE_AD_MATE_HOUR_STATICS_ADD_FAIL                          = 11584;
    const CODE_AD_MATE_HOUR_STATICS_GET_SUCCESS                       = 11585;
    const CODE_AD_MATE_HOUR_STATICS_NOT_FOUND                         = 11586;
    const CODE_AD_MATE_HOUR_STATICS_DELETE_SUCCESS                    = 11587;
    const CODE_AD_MATE_HOUR_STATICS_DELETE_FAIL                       = 11588;
    const CODE_AD_MATE_HOUR_STATICS_UPDATE_SUCCESS                    = 11589;
    const CODE_AD_MATE_HOUR_STATICS_UPDATE_FAIL                       = 11590;
    
    const CODE_AD_MATE_ADD_SUCCESS                       = 11591;
    const CODE_AD_MATE_ADD_FAIL                          = 11592;
    const CODE_AD_MATE_GET_SUCCESS                       = 11593;
    const CODE_AD_MATE_NOT_FOUND                         = 11594;
    const CODE_AD_MATE_DELETE_SUCCESS                    = 11595;
    const CODE_AD_MATE_DELETE_FAIL                       = 11596;
    const CODE_AD_MATE_UPDATE_SUCCESS                    = 11597;
    const CODE_AD_MATE_UPDATE_FAIL                       = 11598;


    const CODE_ADUSER_STATICS_ADD_SUCCESS                       = 11599;
    const CODE_ADUSER_STATICS_ADD_FAIL                          = 11600;
    const CODE_ADUSER_STATICS_GET_SUCCESS                       = 11601;
    const CODE_ADUSER_STATICS_NOT_FOUND                         = 11602;
    const CODE_ADUSER_STATICS_DELETE_SUCCESS                    = 11603;
    const CODE_ADUSER_STATICS_DELETE_FAIL                       = 11604;
    const CODE_ADUSER_STATICS_UPDATE_SUCCESS                    = 11605;
    const CODE_ADUSER_STATICS_UPDATE_FAIL                       = 11606;

    const CODE_ADUSER_DAILY_STATICS_ADD_SUCCESS                       = 11607;
    const CODE_ADUSER_DAILY_STATICS_ADD_FAIL                          = 11608;
    const CODE_ADUSER_DAILY_STATICS_GET_SUCCESS                       = 11609;
    const CODE_ADUSER_DAILY_STATICS_NOT_FOUND                         = 11610;
    const CODE_ADUSER_DAILY_STATICS_DELETE_SUCCESS                    = 11611;
    const CODE_ADUSER_DAILY_STATICS_DELETE_FAIL                       = 11612;
    const CODE_ADUSER_DAILY_STATICS_UPDATE_SUCCESS                    = 11613;
    const CODE_ADUSER_DAILY_STATICS_UPDATE_FAIL                       = 11614;


    const CODE_ADUSER_HOUR_STATICS_ADD_SUCCESS                       = 11615;
    const CODE_ADUSER_HOUR_STATICS_ADD_FAIL                          = 11616;
    const CODE_ADUSER_HOUR_STATICS_GET_SUCCESS                       = 11617;
    const CODE_ADUSER_HOUR_STATICS_NOT_FOUND                         = 11618;
    const CODE_ADUSER_HOUR_STATICS_DELETE_SUCCESS                    = 11619;
    const CODE_ADUSER_HOUR_STATICS_DELETE_FAIL                       = 11620;
    const CODE_ADUSER_HOUR_STATICS_UPDATE_SUCCESS                    = 11621;
    const CODE_ADUSER_HOUR_STATICS_UPDATE_FAIL                       = 11622;


    const CODE_MATE_CLICK_TODAY_ADD_SUCCESS                       = 11623;
    const CODE_MATE_CLICK_TODAY_ADD_FAIL                          = 11624;
    const CODE_MATE_CLICK_TODAY_GET_SUCCESS                       = 11625;
    const CODE_MATE_CLICK_TODAY_NOT_FOUND                         = 11626;
    const CODE_MATE_CLICK_TODAY_DELETE_SUCCESS                    = 11627;
    const CODE_MATE_CLICK_TODAY_DELETE_FAIL                       = 11628;
    const CODE_MATE_CLICK_TODAY_UPDATE_SUCCESS                    = 11629;
    const CODE_MATE_CLICK_TODAY_UPDATE_FAIL                       = 11630;


    const CODE_MATE_CLICK_MONTH_ADD_SUCCESS                       = 11631;
    const CODE_MATE_CLICK_MONTH_ADD_FAIL                          = 11632;
    const CODE_MATE_CLICK_MONTH_GET_SUCCESS                       = 11633;
    const CODE_MATE_CLICK_MONTH_NOT_FOUND                         = 11634;
    const CODE_MATE_CLICK_MONTH_DELETE_SUCCESS                    = 11635;
    const CODE_MATE_CLICK_MONTH_DELETE_FAIL                       = 11636;
    const CODE_MATE_CLICK_MONTH_UPDATE_SUCCESS                    = 11637;
    const CODE_MATE_CLICK_MONTH_UPDATE_FAIL                       = 11638;



    const CODE_WEB_SERVER_ADD_SUCCESS                       = 11639;
    const CODE_WEB_SERVER_ADD_FAIL                          = 11640;
    const CODE_WEB_SERVER_GET_SUCCESS                       = 11641;
    const CODE_WEB_SERVER_NOT_FOUND                         = 11642;
    const CODE_WEB_SERVER_DELETE_SUCCESS                    = 11643;
    const CODE_WEB_SERVER_DELETE_FAIL                       = 11644;
    const CODE_WEB_SERVER_UPDATE_SUCCESS                    = 11645;
    const CODE_WEB_SERVER_UPDATE_FAIL                       = 11646;
    
    const CODE_AD_BACK_ARELADY_EXISTS          = 11651;
    const CODE_AD_BACK_ADD_SUCCESS             = 11652;
    const CODE_AD_BACK_ADD_FAIL                = 11653;
    const CODE_AD_BACK_GET_SUCCESS             = 11654;
    const CODE_AD_BACK_NOT_FOUND               = 11655;
    const CODE_AD_BACK_DELETE_SUCCESS          = 11656;
    const CODE_AD_BACK_DELETE_FAIL             = 11657;
    const CODE_AD_BACK_UPDATE_SUCCESS          = 11658;
    const CODE_AD_BACK_UPDATE_FAIL             = 11659;
    
    
    
    const CODE_BOX_ADD_SUCCESS                       = 11660;
    const CODE_BOX_ADD_FAIL                          = 11661;
    const CODE_BOX_GET_SUCCESS                       = 11662;
    const CODE_BOX_NOT_FOUND                         = 11663;
    const CODE_BOX_DELETE_SUCCESS                    = 11664;
    const CODE_BOX_DELETE_FAIL                       = 11665;
    const CODE_BOX_UPDATE_SUCCESS                    = 11666;
    const CODE_BOX_UPDATE_FAIL                       = 11667;

    const CODE_USER_BOX_ADD_SUCCESS                       = 11680;
    const CODE_USER_BOX_ADD_FAIL                          = 11681;
    const CODE_USER_BOX_GET_SUCCESS                       = 11682;
    const CODE_USER_BOX_NOT_FOUND                         = 11683;
    const CODE_USER_BOX_DELETE_SUCCESS                    = 11684;
    const CODE_USER_BOX_DELETE_FAIL                       = 11685;
    const CODE_USER_BOX_UPDATE_SUCCESS                    = 11686;
    const CODE_USER_BOX_UPDATE_FAIL                       = 11687;
    const CODE_USER_BOX_ALREADY_EXISTS                    = 11688;
    const CODE_USER_BOX_OPEN_FAIL                         = 11689;
    const CODE_USER_BOX_OPEN_SUCCESS                      = 11690;

    const CODE_IP_POOL_ARELADY_EXISTS          = 11691;
    const CODE_IP_POOL_ADD_SUCCESS             = 11692;
    const CODE_IP_POOL_ADD_FAIL                = 11693;
    const CODE_IP_POOL_GET_SUCCESS             = 11694;
    const CODE_IP_POOL_NOT_FOUND               = 11695;
    const CODE_IP_POOL_DELETE_SUCCESS          = 11696;
    const CODE_IP_POOL_DELETE_FAIL             = 11697;
    const CODE_IP_POOL_UPDATE_SUCCESS          = 11698;
    const CODE_IP_POOL_UPDATE_FAIL             = 11699;

    const CODE_USER_BOX_KEY_ADD_SUCCESS                       = 11700;
    const CODE_USER_BOX_KEY_ADD_FAIL                          = 11701;
    const CODE_USER_BOX_KEY_GET_SUCCESS                       = 11702;
    const CODE_USER_BOX_KEY_NOT_FOUND                         = 11703;
    const CODE_USER_BOX_KEY_DELETE_SUCCESS                    = 11704;
    const CODE_USER_BOX_KEY_DELETE_FAIL                       = 11705;
    const CODE_USER_BOX_KEY_UPDATE_SUCCESS                    = 11706;
    const CODE_USER_BOX_KEY_UPDATE_FAIL                       = 11707;
    const CODE_USER_BOX_KEY_NOT_ENOUGN                        = 11708;
    const CODE_USER_BOX_KEY_STEAL_SUCCESS                     = 11709;
    const CODE_USER_BOX_KEY_STEAL_FAIL                        = 11710;
    const CODE_STEAL_KEY_EXCEED_LIMIT_TIME                    = 11711; //20点以后可以偷钥匙
    const CODE_STEAL_KEY_NEED_COMPLETE_SIGN                   = 11712; //需完成今日的3次签到
    const CODE_ONLY_CAN_STEAL_NOSIGN_FRIEND                   = 11713; //只能偷今日没有签到的好友(徒弟、徒孙)
    const CODE_ONLY_CAN_STEAL_FRIEND                          = 11714; //只能偷徒弟、徒孙的钥匙
    const CODE_STEAL_FAIL_NOT_ENOUGH                          = 11715; //好友钥匙数量不足
    const CODE_TODAY_ALREADY_STEAL                            = 11716; //一天只能偷取一次
    


    const CODE_USER_SIGN_ADD_SUCCESS                       = 11750;
    const CODE_USER_SIGN_ADD_FAIL                          = 11751;
    const CODE_USER_SIGN_GET_SUCCESS                       = 11752;
    const CODE_USER_SIGN_NOT_FOUND                         = 11753;
    const CODE_USER_SIGN_DELETE_SUCCESS                    = 11754;
    const CODE_USER_SIGN_DELETE_FAIL                       = 11755;
    const CODE_USER_SIGN_UPDATE_SUCCESS                    = 11756;
    const CODE_USER_SIGN_UPDATE_FAIL                       = 11757;
    const CODE_CURRENT_PERIOD_ALREADY_SIGN                 = 11758;



    const CODE_BOX_OPEN_HIS_ADD_SUCCESS                       = 11770;
    const CODE_BOX_OPEN_HIS_ADD_FAIL                          = 11771;
    const CODE_BOX_OPEN_HIS_GET_SUCCESS                       = 11772;
    const CODE_BOX_OPEN_HIS_NOT_FOUND                         = 11773;
    const CODE_BOX_OPEN_HIS_DELETE_SUCCESS                    = 11774;
    const CODE_BOX_OPEN_HIS_DELETE_FAIL                       = 11775;
    const CODE_BOX_OPEN_HIS_UPDATE_SUCCESS                    = 11776;
    const CODE_BOX_OPEN_HIS_UPDATE_FAIL                       = 11777;



    const CODE_USER_REWARD_HIS_ADD_SUCCESS                       = 11790;
    const CODE_USER_REWARD_HIS_ADD_FAIL                          = 11791;
    const CODE_USER_REWARD_HIS_GET_SUCCESS                       = 11792;
    const CODE_USER_REWARD_HIS_NOT_FOUND                         = 11793;
    const CODE_USER_REWARD_HIS_DELETE_SUCCESS                    = 11794;
    const CODE_USER_REWARD_HIS_DELETE_FAIL                       = 11795;
    const CODE_USER_REWARD_HIS_UPDATE_SUCCESS                    = 11796;
    const CODE_USER_REWARD_HIS_UPDATE_FAIL                       = 11797;
    const CODE_USER_REWARD_HIS_INVALID_STATUS                    = 11798;


    const CODE_REWARD_ADD_SUCCESS                       = 11830;
    const CODE_REWARD_ADD_FAIL                          = 11831;
    const CODE_REWARD_GET_SUCCESS                       = 11832;
    const CODE_REWARD_NOT_FOUND                         = 11833;
    const CODE_REWARD_DELETE_SUCCESS                    = 11834;
    const CODE_REWARD_DELETE_FAIL                       = 11835;
    const CODE_REWARD_UPDATE_SUCCESS                    = 11836;
    const CODE_REWARD_UPDATE_FAIL                       = 11837;



    const CODE_CHANNEL_ADD_SUCCESS                       = 12000;
    const CODE_CHANNEL_ADD_FAIL                          = 12001;
    const CODE_CHANNEL_GET_SUCCESS                       = 12002;
    const CODE_CHANNEL_NOT_FOUND                         = 12003;
    const CODE_CHANNEL_DELETE_SUCCESS                    = 12004;
    const CODE_CHANNEL_DELETE_FAIL                       = 12005;
    const CODE_CHANNEL_UPDATE_SUCCESS                    = 12006;
    const CODE_CHANNEL_UPDATE_FAIL                       = 12007;


    const CODE_OPENID_STATICS_ADD_SUCCESS                       = 12100;
    const CODE_OPENID_STATICS_ADD_FAIL                          = 12101;
    const CODE_OPENID_STATICS_GET_SUCCESS                       = 12102;
    const CODE_OPENID_STATICS_NOT_FOUND                         = 12103;
    const CODE_OPENID_STATICS_DELETE_SUCCESS                    = 12104;
    const CODE_OPENID_STATICS_DELETE_FAIL                       = 12105;
    const CODE_OPENID_STATICS_UPDATE_SUCCESS                    = 12106;
    const CODE_OPENID_STATICS_UPDATE_FAIL                       = 12107;


    const CODE_TG_USER_MOBILE_ADD_SUCCESS                       = 12200;
    const CODE_TG_USER_MOBILE_ADD_FAIL                          = 12201;
    const CODE_TG_USER_MOBILE_GET_SUCCESS                       = 12202;
    const CODE_TG_USER_MOBILE_NOT_FOUND                         = 12203;
    const CODE_TG_USER_MOBILE_DELETE_SUCCESS                    = 12204;
    const CODE_TG_USER_MOBILE_DELETE_FAIL                       = 12205;
    const CODE_TG_USER_MOBILE_UPDATE_SUCCESS                    = 12206;
    const CODE_TG_USER_MOBILE_UPDATE_FAIL                       = 12207;


    const CODE_CHANNEL_SHORT_PARAM_ADD_SUCCESS                       = 12300;
    const CODE_CHANNEL_SHORT_PARAM_ADD_FAIL                          = 12301;
    const CODE_CHANNEL_SHORT_PARAM_GET_SUCCESS                       = 12302;
    const CODE_CHANNEL_SHORT_PARAM_NOT_FOUND                         = 12303;
    const CODE_CHANNEL_SHORT_PARAM_DELETE_SUCCESS                    = 12304;
    const CODE_CHANNEL_SHORT_PARAM_DELETE_FAIL                       = 12305;
    const CODE_CHANNEL_SHORT_PARAM_UPDATE_SUCCESS                    = 12306;
    const CODE_CHANNEL_SHORT_PARAM_UPDATE_FAIL                       = 12307;
    
    
}
