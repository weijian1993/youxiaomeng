<?php
// 事件定义文件
return [
    'bind' => [
//        'UserLog' => 'app\listener\UserLog',
    ],

    'listen' => [
        'AppInit'  => [
//          'app\listener\UserLog',
        ],

        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
    ],

    'subscribe' => [
    ],
];
