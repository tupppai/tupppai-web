<?php
return [
    'debug'  => true,
    'use_alias'    => env('WECHAT_USE_ALIAS', false),
    'app_id'       => env('MP_APPID', 'YourAppId'), // 必填
    'secret'       => env('MP_APPSECRET', 'YourSecret'), // 必填
    'token'        => env('MP_TOKEN', 'YourToken'),  // 必填
    // 加密模式需要，其它模式不需要
    'encoding_key' => env('MP_AES_KEY', 'YourEncodingAESKey'),
    'log' => [
        'level' => 'debug',
        'file'  => storage_path('wechat/wx_'.date('Ymd').'.log')
    ]
];
