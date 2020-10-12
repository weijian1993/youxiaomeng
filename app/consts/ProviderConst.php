<?php

namespace app\consts;


use app\services\AdminService;
use app\services\FinanceService;
use app\services\MemberService;
use app\services\OperateService;
use app\services\PaymentService;
use app\services\SchoolService;
use app\services\SystemService;

class ProviderConst
{
    // 系统服务
    CONST SYSTEM_SERVICE = 'system_service';
    // 后台服务
    CONST ADMIN_SERVICE = 'admin_service';
    // 用户服务
    CONST MEMBER_SERVICE = 'member_service';
    // 支付服务
    CONST PAYMENT_SERVICE = 'payment_service';
    // 运营服务
    CONST OPERATE_SERVICE = 'operate_service';
    // 财务服务
    CONST FINANCE_SERVICE = 'finance_service';
    // 学员服务
    CONST SCHOOL_SERVICE = 'school_service';
    /**
     * 所有服务
     */
    CONST SERVICES = [
        self::SYSTEM_SERVICE  => SystemService::class,
        self::ADMIN_SERVICE   => AdminService::class,
        self::MEMBER_SERVICE  => MemberService::class,
        self::PAYMENT_SERVICE => PaymentService::class,
        self::OPERATE_SERVICE => OperateService::class,
        self::FINANCE_SERVICE => FinanceService::class,
        self::SCHOOL_SERVICE => SchoolService::class,
    ];
}
