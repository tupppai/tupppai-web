<?php

namespace App\Service;

use \App\Models\Evaluation as mEvaluation;

class Evaluation extends ServiceBase
{
    public static function set_evaluation($uid, $content){
        $evaluation = mEvaluation::findFirst(array("uid = {$uid} AND content = '{$content}'"));
        if(!$evaluation) {
            $evaluation = new mEvaluation;
            $evaluation->uid = $uid;
            $evaluation->create_time = time();
        }

        $evaluation->content = is_null($content)?'': $content;
        $evaluation->update_time = time();
        return $evaluation->save_and_return($evaluation, true);
    }
}
