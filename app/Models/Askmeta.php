<?php

namespace App\Models;

class Askmeta extends ModelBase
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     * 外键ID，即 ask_id
     *
     * @var integer
     */
    public $fid;

    /**
     *
     * @var string
     */
    public $key;

    /**
     *
     * @var string
     */
    public $value;

    protected $table = 'askmeta';
    protected $primaryKey = 'ameta_id';
    protected $guarded = ['ameta_id'];
    public $timestamps = false;

    public function initialize()
    {
        $this->belongsTo("ask_id", "App\Models\Ask", "id", array(
            'alias' => 'Ask'
        ));
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'ameta_id'    => 'id',
            'ask_id'      => 'fid',
            'ameta_key'   => 'key',
            'ameta_value' => 'value'
        );
    }

    public function get( $ask_id, $key ){
        return $this->where([
            'ask_id' => $ask_id,
            'ameta_key' => $key
        ])->first();
    }

    public function set( $ask_id, $key, $value ){
        $cond = ['ask_id' => $ask_id, 'ameta_key' => $key ];
        $data = $cond;
        $data['ameta_value'] = $value;
        return $this->updateOrCreate( $cond, $data );
    }
}
