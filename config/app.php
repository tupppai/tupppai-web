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


    'key' => env('APP_KEY', 'xnAnWWq0svOey0PRtBSkaNCOBBxDZHBT'),

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
        'appid'      => env('TX_APPID'),
        'appkey'     => env('TX_APPKEY'),
        'server_name'=> env('TX_SERVER_NAME'), 
        //根据是否开发环境选择 api 服务器
    ),

    /**
     * qiniu cloud cdn
     */
    'qiniu' => array(
        'ak'    => env('QINIU_AK'),
        'sk'    => env('QINIU_SK'),
        'domain'=> env('QINIU_DOMAIN'),
        'bucket'=> env('QINIU_BUCKET'),
    ),

    'public_dir'    => env('IMAGE_PUBLIC_DIR'),
    'upload_dir'    => env('IMAGE_UPLOAD_DIR'),
    'preview_dir'   => env('IMAGE_PREVIEW_DIR')
];
