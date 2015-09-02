<?php
namespace App\Models;

class Usermeta extends ModelBase
{

    protected $table = 'usermeta';
    protected $primaryKey = 'umeta_id';
    //protected $fillable = [ 'uid', 'umeta_str_value', 'umeta_int_value', 'umeta_key' ];
    protected $guarded = ['umeta_id'];
    const KEY_REMARK = 'remark';
    const KEY_FORBID = 'forbid_speech'; //禁言

    const KEY_LAST_READ_COMMENT = 'last_read_comment';
    const KEY_LAST_READ_FOLLOW  = 'last_read_fellow';
    const KEY_LAST_READ_INVITE  = 'last_read_invite';
    const KEY_LAST_READ_REPLY   = 'last_read_reply';
    const KEY_LAST_READ_NOTICE  = 'last_read_notice';

    const KEY_STAFF_TIME_PRICE_RATE = 'staff_time_price_rate';

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $uid;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $str_value;

    /**
     * @var integer
     */
    public $int_value;


    public function initialize()
    {
        $this->belongsTo("uid", "App\Models\User", "uid", array(
            'alias' => 'User'
        ));
    }

}
