<?php namespace App\Services;

use App\Models\Evaluation as mEvaluation;

class Evaluation extends ServiceBase
{
    public static function setEvaluation($uid, $content){
        $mEvaluation = new mEvaluation;

        $evaluation = $mEvaluation->get_evaluation($uid, $content);
        if(!$evaluation) {
            $evaluation = self::addNewEvaluation($uid, $content);
        }
        else {
            $evaluation->content = is_null($content)?'': $content;
            $evaluation->save();
        }
        return $evaluation;
    }

    public static function addNewEvaluation($uid, $content) {
        $evaluation = new mEvaluation;
        $evaluation->assign(array(
            'uid'=>$uid,
            'content'=>$content
        ));

        return $evaluation;
    }

    public static function getUserEvaluations($uid) {
        $mEvaluation = new mEvaluation;
        $evaluations = $mEvaluation->get_evaluations_by_uid($uid);

        return $evaluations;
    }
}
