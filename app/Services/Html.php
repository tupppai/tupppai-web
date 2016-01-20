<?php namespace App\Services;

use App\Models\Html as mHtml;

use App\Services\ActionLog as sActionLog;

class Html extends ServiceBase{

    public static function addNewHtml( $uid, $create_by, $title, $path, $url){
        $html = new mHtml();
        sActionLog::init( 'ADD_HTML', $html);

        $html->assign(array(
            'uid'       => $uid,
            'create_by' => $create_by,
            'update_by' => $create_by,
            'title'     => $title,
            'path'      => $path,
            'url'       => $url,
            'status'    => mHtml::STATUS_NORMAL
        ));
        $html->save();
        sActionLog::save( $html);

        return $html;
    }

    public static function getHtmlById($id) {
        return (new mHtml)->get_html_by_id($id);
    }
}
