<?php


namespace App\Models;


class Reward extends ModelBase
{
    protected $table = 'rewards';
    protected $fillable = ['uid', 'askid', 'amount'];

    public function setAmountAttribute($value)
    {
        if (0 > $value) {
            throw new \Exception('Reward field amount  不能为负数 ');
        }
        $this->attributes['amount'] = $value * 1000;
    }

    public function getAmountAttribute($value)
    {
        return $value / 1000;
    }
}