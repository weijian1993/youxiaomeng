<?php

use app\service\AppService;
use app\service\AdminService;
use  \app\service\UrlService;

// 系统服务定义文件
// 服务在完成全局初始化之后执行
return [
    AppService::class,
    AdminService::class,
    UrlService::class,
];
