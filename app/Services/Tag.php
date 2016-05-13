<?php namespace App\Services;

use \App\Models\Tag as mTag;
use App\Services\ActionLog as sActionLog;

class Tag extends ServiceBase{

    public static function addNewTag( $uid, $name ) {
        sActionLog::init( 'ADD_NEW_TAG' );

        $tag = new mTag;
        $tag->assign(array(
            'uid' => $uid,
            'name'=>$name
        ));

        $tag->save();
        sActionLog::save( $tag );
        return $tag;
    }

    public static function updateTag( $uid, $id, $name, $status = null){
        $mTag = new mTag;

        sActionLog::init( 'ADD_NEW_TAG' );
        $tag  = $mTag->get_tag_by_id($id);

        if (!$tag) {
            sActionLog::init( 'UPDATE_TAG', $tag );
            $tag = $mTag;
        }
        $tag->name = $name;
        if(isset($status)) {
            $tag->status = $status;
        }

        $tag->save();
        sActionLog::save( $tag );
        return $tag;
    }

    public static function deleteTag( $uid, $tag_id ){
        $mTag = new mTag();
        $tag = $mTag->where(['id' => $tag_id])->first();
        if( !$tag ){
            return error('TAG_NOT_EXIST');
        }

        $tag->assign([
            'status' => $mTag::STATUS_DELETED,
            'delete_by' => $uid,
            'delete_time' => time()
        ])->save();

        return $tag;
    }

    public static function getTagById ($id) {
        $tag = (new mTag)->get_tag_by_id($id);

        return $tag;
    }

    public static function getTagByPid ($uid) {
        $tag = (new mTag)->get_tag_by_uid($uid);

        return $tag;
    }

    public static function getTags($page, $size) {
        $tags = (new mTag)->get_tags($page, $size);
        return $tags;
    }

    public static function getTagsByCond($cond, $page, $size) {
        $tags = (new mTag)->get_tags($page, $size);

        $data = array();
        foreach($tags as $tag) {
            $data[] = self::brief($tag);
        }
        return $data;
    }

    public static function getTagsByName( $name ){
        $tags = (new mTag)->where( 'name', 'LIKE', $name.'%' )->get();

        return $tags;
    }

    public static function getTagByName( $name )
    {
        return (new mTag)->select(['id','name'])->where( 'name', $name )->first();
    }

    public static function getTagsLikeName($name)
    {
        return (new mTag())->select(['id as tag_id','name'])->where( 'name', 'LIKE', "%{$name}%" )->get();
    }

    public static function searchTag($cond, $page, $limit)
    {
        $tag = new mTag();

        if(isset($cond['like_name'])){
            $tag = $tag->where( 'name', 'LIKE', "%{$cond['like_name']}%" );
        }

        if(!isset($cond['no_page'])){
            $tag = $tag->forPage($page,$limit)->get();
        }
    }

    public static function brief($tag) {
        $data = array();
        $data['id'] = $tag->id;
        $data['name'] = $tag->name;

        return $data;
    }
}
