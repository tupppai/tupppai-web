<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = env('BBS_DB_HOST');
$db['default']['port'] = env('BBS_DB_PORT');
$db['default']['username'] = env('BBS_DB_USERNAME');
$db['default']['password'] = env('BBS_DB_PASSWORD');
$db['default']['database'] = env('BBS_DB_DATABASE');
$db['default']['dbdriver'] = env('BBS_DB_CONNECTION');
$db['default']['dbprefix'] = env('BBS_DB_PREFIX');
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */

