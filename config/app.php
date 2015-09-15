<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'SomeRandomString!!!'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /**
     * tencent auth api
     */
    'txsdk' => array(
        'appid'      => env('TX_APPID', '100645734'),
        'appkey'     => env('TX_APPKEY', '4c721e5ac32aa6062744d4fe64db01b4'),
        'server_name'=> env('TX_SERVER_NAME', 'openapi.tencentyun.com'), 
        //根据是否开发环境选择 api 服务器
    ),

    /**
     * qiniu cloud cdn
     */
    'qiniu' => array(
        'ak'    => env('QINIU_AK', 'eifvG4u-0Wp9KZgsev_9MyBiBRXHcOFaeSOXJ19f'),
        'sk'    => env('QINIU_SK', 'xDdcSRN2s0hGw3djcBKnrOMCHN8jWEQgjBCxbisr'),
        'domain'=> env('QINIU_DOMAIN', '7u2spr.com1.z0.glb.clouddn.com'),
        'bucket'=> env('QINIU_BUCKET', 'pstest'),
    ),

    'public_dir'    => env('IMAGE_PUBLIC_DIR'),
    'upload_dir'    => env('IMAGE_UPLOAD_DIR'),
    'preview_dir'   => env('IMAGE_PREVIEW_DIR')
];
