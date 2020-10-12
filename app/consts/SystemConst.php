<?php

namespace app\consts;

class SystemConst
{
    /**
     * 网站配置
     */
    CONST CONFIG_WEBSITE = 'website';
    CONST CONFIG_FILESITE = 'filesite';
    CONST CONFIG_WXSITE = 'wxsite';
    CONST CONFIG_ALISITE = 'alisite';
    CONST CONFIG_SMSSITE = 'smssite';
    CONST CONFIG_PUSHSITE = 'pushsite';
    CONST CONFIG_MONEYSITE = 'moneysite';
    CONST CONFIG_INVITESITE = 'invitesite';
    CONST CONFIG_CHECKSITE = 'checksite';
    CONST CONFIG_QINIUSITE = 'qiniusite';
    CONST CONFIG_SITES = [
        self::CONFIG_WEBSITE    => '网站设置',
        self::CONFIG_FILESITE   => '附件设置',
        self::CONFIG_WXSITE     => '微信参数',
        self::CONFIG_ALISITE    => '支付宝参数',
        self::CONFIG_SMSSITE    => '短信配置',
        self::CONFIG_PUSHSITE   => '推送配置',
        self::CONFIG_MONEYSITE  => '充值配置',
        self::CONFIG_INVITESITE => '邀请奖励',
        self::CONFIG_CHECKSITE  => '签到设置',
        self::CONFIG_QINIUSITE  => '七牛云参数',
    ];

    /**
     * 图片上传引擎
     */
    CONST FILE_TYPE_ALI = 'ali';
    CONST FILE_TYPE_QINIU = 'qiniu';
    CONST FILE_TYPE_LOCAL = 'local';
    CONST FILE_TYPES = [
        self::FILE_TYPE_ALI   => '阿里云',
        self::FILE_TYPE_QINIU => '七牛云',
        self::FILE_TYPE_LOCAL => '本地'
    ];

    /**
     * 软删除规则
     */
    CONST CLOSED_OFF = 0;
    CONST CLOSED_ON = 1;
    CONST CLOSEDS = [
        self::CLOSED_ON  => '已删除',
        self::CLOSED_OFF => '未删除'
    ];

    /**
     * 审核字段
     */
    CONST AUDIT_ON = 1;
    CONST AUDIT_OFF = 0;
    CONST AUDITS = [
        self::AUDIT_ON  => '已审核',
        self::AUDIT_OFF => '待审核'
    ];

    /**
     * 默认分页数
     */
    CONST DEFAULT_LIMIT = 15;

    /**
     * 权限类型
     */
    CONST AUTH_TYPE_MENU = 'menu';
    CONST AUTH_TYPE_BUTTON = 'button';
    CONST AUTH_TYPES = [
        self::AUTH_TYPE_MENU   => '菜单',
        self::AUTH_TYPE_BUTTON => '按钮'
    ];


    /**
     * 权限显示
     */
    CONST AUTH_HIDDEN_ON = 1;
    CONST AUTH_HIDDEN_OFF = 0;
    CONST AUTH_HIDDENS = [
        self::AUTH_HIDDEN_ON  => '隐藏',
        self::AUTH_HIDDEN_OFF => '显示'
    ];

    /**
     * 权限显示
     */
    CONST AUTH_RECORD_ON = 1;
    CONST AUTH_RECORD_OFF = 0;
    CONST AUTH_RECORDS = [
        self::AUTH_RECORD_ON  => '打开记录',
        self::AUTH_RECORD_OFF => '关闭记录'
    ];
    /**
     * 评论显示
     */
    CONST REPLY_SHOW_OFF = 1;
    CONST REPLY_SHOW_ON = 2;
    CONST REPLY_SHOW = [
        self::REPLY_SHOW_OFF => '隐藏',
        self::REPLY_SHOW_ON => '显示'
    ];
    /**
     * 评论是否有图片
     */
    CONST REPLY_IMG_OFF = 1;
    CONST REPLY_IMG_ON = 2;
    CONST REPLY_IMG = [
        self::REPLY_IMG_OFF => '无',
        self::REPLY_IMG_ON => '有'
    ];

    /**
     * 角色类型
     */
    CONST ROLE_ROOT_ON = 1;
    CONST ROLE_ROOT_OFF = 0;
    CONST ROLE_ROOTS = [
        self::ROLE_ROOT_ON  => '超管',
        self::ROLE_ROOT_OFF => '普通'
    ];

    /**
     * 推送设备类型
     */
    CONST DEVICE_FROM_MEMBER = 'member';
    CONST DEVICE_FROMS = [
        self::DEVICE_FROM_MEMBER => '用户'
    ];

    /**
     * 用户性别
     */
    CONST MEMBER_SEX_MAN = 1;
    CONST MEMBER_SEX_WOMAN = 2;
    CONST MEMBER_SEXS = [
        self::MEMBER_SEX_MAN   => '男',
        self::MEMBER_SEX_WOMAN => '女'
    ];

    /**
     * 用户地址类型
     */
    CONST MEMBER_ADDR_TYPE_COMPANY = 1;
    CONST MEMBER_ADDR_TYPE_HOUSE = 2;
    CONST MEMBER_ADDR_TYPE_SCHOOL = 3;
    CONST MEMBER_ADDR_TYPE_OTHER = 4;
    CONST MEMBER_ADDR_TYPES = [
        self::MEMBER_ADDR_TYPE_COMPANY => '公司',
        self::MEMBER_ADDR_TYPE_HOUSE   => '住宅',
        self::MEMBER_ADDR_TYPE_SCHOOL  => '学校',
        self::MEMBER_ADDR_TYPE_OTHER   => '其他'
    ];

    /**
     * 用户地址默认
     */
    CONST MEMBER_ADDR_DEFAULT_ON = 1;
    CONST MEMBER_ADDR_DEFAULT_OFF = 0;
    CONST MEMBER_ADDR_DEFAULTS = [
        self::MEMBER_ADDR_DEFAULT_ON  => '默认',
        self::MEMBER_ADDR_DEFAULT_OFF => '普通'
    ];

    /**
     * 地区地址类型
     */
    CONST REGION_LEVEL_PROVINCE = 1;
    CONST REGION_LEVEL_CITY = 2;
    CONST REGION_LEVEL_AREA = 3;
    CONST REGION_LEVELS = [
        self::REGION_LEVEL_PROVINCE => '省份',
        self::REGION_LEVEL_CITY     => '城市',
        self::REGION_LEVEL_AREA     => '区县'
    ];

    /**
     * 用户消息已读
     */
    CONST MEMBER_MSG_READ_ON = 1;
    CONST MEMBER_MSG_READ_OFF = 0;
    CONST MEMBER_MSG_READS = [
        self::MEMBER_MSG_READ_ON  => '已读',
        self::MEMBER_MSG_READ_OFF => '未读'
    ];

    /**
     * 用户消息类型
     */
    CONST MEMBER_MSG_TYPE_SYSTEM = 'system';
    CONST MEMBER_MSG_TYPE_ORDER = 'order';
    CONST MEMBER_MSG_TYPE_INTEGRAL = 'integral';
    CONST MEMBER_MSG_TYPE_COUPON = 'coupon';
    CONST MEMBER_MSG_TYPE_HONGBAO = 'hongbao';
    CONST MEMBER_MSG_TYPES = [
        self::MEMBER_MSG_TYPE_SYSTEM   => '系统',
        self::MEMBER_MSG_TYPE_ORDER    => '订单',
        self::MEMBER_MSG_TYPE_INTEGRAL => '积分',
        self::MEMBER_MSG_TYPE_COUPON   => '优惠券',
        self::MEMBER_MSG_TYPE_HONGBAO  => '红包',
    ];

    /**
     * 支付接口状态
     */
    CONST PAYMENT_STATUS_OPEN = 1;
    CONST PAYMENT_STATUS_CLOSE = 0;
    CONST PAYMENT_STATUSS = [
        self::PAYMENT_STATUS_OPEN  => '开启',
        self::PAYMENT_STATUS_CLOSE => '关闭',
    ];

    /**
     * 支付接口状态
     */
    CONST PAYMENT_REFUND_ON = 1;
    CONST PAYMENT_REFUND_OFF = 0;
    CONST PAYMENT_REFUNDS = [
        self::PAYMENT_REFUND_ON  => '已退款',
        self::PAYMENT_REFUND_OFF => '未退款',
    ];

    /**
     * 支付来源
     */
    CONST PAYMENT_FROM_MONEY = 'money';
    CONST PAYMENT_FROM_ORDER = 'order';
    CONST PAYMENT_FROMS = [
        self::PAYMENT_FROM_MONEY => '余额充值',
        self::PAYMENT_FROM_ORDER => '订单支付',
    ];

    /**
     * 支付类型
     */
    CONST PAYMENT_TYPE_PAY = 'pay';
    CONST PAYMENT_TYPE_REFUND = 'refund';
    CONST PAYMENT_TYPES = [
        self::PAYMENT_TYPE_PAY    => '付款',
        self::PAYMENT_TYPE_REFUND => '退款',
    ];

    /**
     * 支付状态
     */
    CONST PAYMENT_PAYED_ON = 1;
    CONST PAYMENT_PAYED_OFF = 0;
    CONST PAYMENT_PAYEDS = [
        self::PAYMENT_PAYED_ON  => '已支付',
        self::PAYMENT_PAYED_OFF => '未支付',
    ];

    /**
     * 支付途径
     */
    CONST PAYMENT_TRADETYPE_APP = 'app';
    CONST PAYMENT_TRADETYPE_WEB = 'web';
    CONST PAYMENT_TRADETYPE_MINI = 'mini';
    CONST PAYMENT_TRADETYPE_SCAN = 'scan';
    CONST PAYMENT_TRADETYPE_CODE = 'code';
    CONST PAYMENT_TRADETYPES = [
        self::PAYMENT_TRADETYPE_APP  => 'APP支付',
        self::PAYMENT_TRADETYPE_WEB  => '网页支付',
        self::PAYMENT_TRADETYPE_MINI => '小程序支付',
        self::PAYMENT_TRADETYPE_CODE => '二维码支付',
        self::PAYMENT_TRADETYPE_SCAN => '付款码支付',
    ];

    /**
     * 支付接口
     */
    CONST PAYMENT_WXPAY = 'wxpay';
    CONST PAYMENT_ALIPAY = 'alipay';
    CONST PAYMENT_MONEY = 'money';
    CONST PAYMENTS = [
        self::PAYMENT_WXPAY  => '微信支付',
        self::PAYMENT_ALIPAY => '支付宝支付',
        self::PAYMENT_MONEY  => '余额支付',
    ];



    /**
     * 文章类型
     */
    CONST ARTICLE_FROM_BOOK = 'book';
    CONST ARTICLE_FROM_PAGE = 'page';
    CONST ARTICLE_FROMS = [
        self::ARTICLE_FROM_BOOK => '文章',
        self::ARTICLE_FROM_PAGE => '单页'
    ];

    /**
     * 余额变动符号
     */
    CONST MONEY_SUB = 'sub';
    CONST MONEY_ADD = 'add';
    CONST MONEY_CONFIG = [
        self::MONEY_ADD => '余额增加',
        self::MONEY_SUB => '余额减少'
    ];

    /**
     * 余额变动理由
     */
    CONST CHANGE_ORDER = 'order';
    CONST CHANGE_SYSTEM = 'system';
    CONST CHANGE_MONEY = 'system';
    CONST CHANGE_CONFIG = [
        self::CHANGE_MONEY  => '余额',
        self::CHANGE_ORDER  => '订单',
        self::CHANGE_SYSTEM => '系统'
    ];

}
