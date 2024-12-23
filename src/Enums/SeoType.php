<?php

namespace Brucelwayne\SEO\Enums;

enum SeoType: string
{
    case Article = 'Article';  // 驼峰命名
    case WebPage = 'WebPage';  // 驼峰命名
    case AboutPage = 'AboutPage';  // 驼峰命名
    case PrivacyPolicy = 'PrivacyPolicy';  // 驼峰命名
    case TermsOfService = 'TermsOfService';  // 驼峰命名
    case SocialMediaPosting = 'SocialMediaPosting';  // 驼峰命名
    case FAQPage = 'FAQPage';  // 驼峰命名
    case BlogPosting = 'BlogPosting';  // 驼峰命名
    case ContactPage = 'ContactPage';  // 驼峰命名
    case HomePage = 'HomePage';  // 首页
    case WebSite = 'WebSite';  // 整个网站
    case Comment = 'Comment';  // 添加 Comment 类型
    case Review = 'Review';  // 评价或评论
    case BreadcrumbList = 'BreadcrumbList';  // 面包屑导航
    case ImageObject = 'ImageObject';  // 图像对象
    case VideoObject = 'VideoObject';  // 视频对象
    case Event = 'Event';  // 事件
    case Product = 'Product';  // 产品页面
    case Service = 'Service';  // 服务页面
    case JobPosting = 'JobPosting';  // 职位发布页面
}
