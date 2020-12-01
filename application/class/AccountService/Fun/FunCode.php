<?php

namespace AccountService\Fun;

class FunCode{
  	
	//function code just set for admin or system, no need to set for app user and ad user.



	const DASHBOARD          = 'fun_dashboard'; //仪表盘
  	const ADMIN_MANAGE       = 'fun_admin_manage'; //admin管理: 增删改查
	const APPUSER_MANAGE     = 'fun_appuser_manage'; //转客管理: 增删改查
	const WITHDRAWAL_MANAGE  = 'fun_withdrawal_manage'; //提现管理： 提现审核


	const NEWS_CATEGORY_MANAGE = 'fun_news_category_manage';  // 新闻内容管理: 增删改查
	const NEWS_MANAGE          = 'fun_news_manage';  // 新闻内容管理: 增删改查
	const USER_STATICS         = 'fun_user_statics'; //转客数据统计
	const USER_DAILY_STATICS   = 'fun_user_daily_statics'; //转客日数据统计
	const USER_HOUR_STATICS    = 'fun_user_hour_statics'; //转客小时数据统计


	const PLATFORM_STATICS   = 'fun_platform_statics'; //平台日数据统计
	const PLATFORM_DAILY_STATICS   = 'fun_platform_daily_statics'; //平台日数据统计
	const PLATFORM_HOUR_STATICS   = 'fun_platform_hour_statics'; //平台小时数据统计
	const USER_RELATIONSHIP  = 'fun_user_relationship'; //邀请关系
	const SHARE_DETAILS      = 'fun_share_details'; //转发明细
	const EWALLET_DETAILS    = 'fun_ewallet_details'; //资金明细
	const VISIT_DETAILS      = 'fun_visit_details'; //阅读明细


	const SYSTEM_DEDU_SETTING       = 'fun_system_dedu_setting'; //系统扣量设置
	const SYSTEM_USER_GROUP_SETTING = 'fun_system_user_group_setting'; //用户分组设置

	const SYSTEM_NEWS_PRICE_CONFIG_SETTING = 'fun_system_news_price_config_setting'; //时段阅读价设置

	const SYSTEM_WECHAT_CONFIG_SETTING = 'fun_wechat_config_setting'; //有关微信设置


	const SYSTEM_APP_CONFIG_SETTING = 'fun_app_config_setting'; //app设置



	const REWARD_COUNT = 'fun_reward_count'; //抽奖情况概述
	const REWARD_HIS   = 'fun_reward_his';  //抽奖明细


	const BOX_KEY_COUNT   = 'fun_user_key_count';  //用户钥匙


	const CHANNEL_MANAGE = 'fun_channel_manage'; //渠道管理


	const OPENID_STATICS = 'fun_openid_statics'; //openid统计


	const TG_MOBILE_MANAGE = 'fun_tg_mobile_manage'; //推广手机号管理
	
}