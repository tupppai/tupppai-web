<?php
namespace App\Models;

class Usermeta extends ModelBase
{

    protected $table = 'usermeta';
    protected $primaryKey = 'umeta_id';
    //protected $fillable = [ 'uid', 'umeta_str_value', 'umeta_int_value', 'umeta_key' ];
    protected $guarded = ['umeta_id'];
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
