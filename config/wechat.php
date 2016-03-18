<?php
return [
    'use_alias'    => env('WECHAT_USE_ALIAS', false),
    'app_id'       => env('WX_APPID', 'YourAppId'), // 必填
    'secret'       => env('WX_SECRET', 'YourSecret'), // 必填
    'token'        => env('WX_TOKEN', 'YourToken'),  // 必填
    'encoding_key' => env('WX_ENCODING_KEY', 'YourEncodingAESKey') // 加密模式需要，其它模式不需要
];
