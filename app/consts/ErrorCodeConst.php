<?php

namespace app\consts;


use app\services\AdminService;
use app\services\SystemService;

class ErrorCodeConst
{
    // 成功状态码
    const SUCCESS = 0;

    // 错误状态码
    const ERROR = 2001;

    // 未登录去登陆
    const NOT_LOGIN = 1001;

    // 无权限不可访问
    const NOT_ACCESS = 1002;

    // 字段错误
    const FIELD_ERROR = 1003;

    // 错误信息
    const ERROR_MESSAGE = [
        self::SUCCESS     => 'success',
        self::ERROR       => 'error',
        self::NOT_LOGIN   => '未登录无法访问',
        self::NOT_ACCESS  => '无权限无法访问',
        self::FIELD_ERROR => '提交字段不合法'
    ];
}
