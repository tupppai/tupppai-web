<?php

namespace App\Services;
use \App\Models\Label as mLabel;

use \App\Services\ActionLog as sActionLog;

class Label extends ServiceBase
{
    /**
     * 添加一个新标签
     *
     * @param string $content   标签内容
     * @param float  $x         标签位置横轴百分比
     * @param float  $y         标签位置纵轴百分比
     * @param integer$upload_id 文件上传id
     * @param integer$target_id    求pid
     * @return false | \psgod\models\label
     */
    public static function addNewLabel($content, $x, $y, $uid, $direction, $upload_id, $target_id, $type=mLabel::TYPE_ASK)
    {
        sActionLog::init( 'ADDED_LABEL' );
        $obj = new mLabel();
        $obj->content   = $content;
        $obj->x         = $x;
        $obj->y         = $y;
        $obj->uid       = $uid;
        $obj->direction = $direction;
        $obj->upload_id = $upload_id;
        $obj->target_id = $target_id;
        $obj->type      = $type;
        $obj->assign(array(
            'create_time'   => time(),
            'update_time'   => time(),
            'status'        => mLabel::STATUS_NORMAL
        ));

        $obj->save();
        sActionLog::save($obj);

        return $obj;
    }

    /**
     * 获取标签列表
     */
    public static function getLabels($type, $target_id, $page, $size)
    {
        $mLabel = new mLabel;
        $labels = $mLabel->page(array('type'=>$type, 'target_id'=>$target_id), $page, $size);
        return $labels->toArray();
    }

    public function brief($label) {

        return array(
            'id'        =>$label->id,
            'content'   =>$label->content,
            'x'         => $label->x,
            'y'         => $label->y,
            'direction' => $label->direction
        );
    }
}
