<?php

namespace App\Models;

class Evaluation extends ModelBase
{
    protected $table = 'evaluations';

    public function get_evaluations_by_uid($uid) {
        $evaluations = self::where('uid', $uid)
            ->get();

        return $evaluations;
    }

    public function get_evaluation($uid, $content) {
        return self::where('uid', $uid)
            ->where('content', $content)
            ->first();
    }
}
