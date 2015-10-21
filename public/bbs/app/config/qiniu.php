<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array (
  'storage_set' => 'qiniu',
  'accesskey'   => env('QINIU_AK'),
  'secretkey'   => env('QINIU_SK'),
  'bucket'      => env('QINIU_BUCKET'),
  'file_domain' => env('QINIU_DOMAIN')
);
