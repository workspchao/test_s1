<?php


namespace Common\Helper\RocketMessageBroker;

/**
 * Contains all enumerable DynamoDB attribute type values
 */
class RocketTopic
{
    const TOPIC_NEWS_VISIT  = 'topic_news_visit'; //获取新闻时
    const TOPIC_NEWS_READ   = 'topic_news_read'; //阅读新闻N秒后调用的接口
    const TOPIC_AD_VISIT    = 'topic_ad_visit'; //用户获取广告时
    const TOPIC_AD_CLICK    = 'topic_ad_click'; //用户点击广告时
    const TOPIC_MESSAGE     = 'topic_message'; //sms、 notification
    
}
