<?php
namespace App\Models;

class Usermeta extends ModelBase
{

    protected $table = 'usermeta';
    protected $primaryKey = 'umeta_id';
    //protected $fillable = [ 'uid', 'umeta_str_value', 'umeta_int_value', 'umeta_key' ];
    protected $guarded = ['umeta_id'];
 
    public function initialize()
    {
        $this->belongsTo("uid", "App\Models\User", "uid", array(
            'alias' => 'User'
        ));
    }


    // public static function save( $uid, $key, $value, $is_int = false ){
    //     $mUsermeta = new mUsermeta();
    //     $valueCol = $is_int? 'umeta_int_value': 'umeta_str_value';
    //     $cond = [
    //         'uid'=> $uid,
    //         'umeta_key' => $key,
    //     ];
    //     $data = $cond;
    //     $data[$valueCol] = $value;
    //     sActionLog::init( 'SAVE_UMETA' );
    //     $usermeta = $mUsermeta->updateOrCreate( $cond, $data );
    //     sActionLog::save( $usermeta );

    //     return  $usermeta->save();
    // }


}
