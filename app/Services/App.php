<?php
namespace App\Services;

use App\Models\App as mApp,
    App\Models\Ask as mAsk,
    App\Models\Reply as mReply,
    App\Models\Label as mLabel,
    App\Models\Count as mCount,
    App\Models\ThreadCategory as mThreadCategory,
    App\Models\Upload as mUpload;

use App\Services\Label as sLabel,
    App\Services\Reply as sReply,
    App\Services\Ask as sAsk,
    App\Services\User as sUser,
    App\Services\Upload as sUpload,
    App\Services\ThreadCategory as sThreadCategory,
    App\Services\ActionLog as sActionLog;

use App\Counters\AskTimelineShares as cAskTimelineShares;

use App\Facades\CloudCDN;

class App extends ServiceBase{
    public static function addNewApp( $uid, $app_name, $logo_id, $jump_url ){
        $app = new mApp();
        sActionLog::init( 'ADD_APP', $app );
        $app->assign(array(
            'app_name'       => $app_name,
            'logo_upload_id' => $logo_id,
            'jumpurl'        => $jump_url,
            //'create_by'      => $uid
        ));
        $app->save();
        sActionLog::save( $app );

        return $app;
    }

    public static function delApp( $uid, $app_id ){
        $mApp= new mApp();
        $app = $mApp->get_app_by_id($app_id);
        if( !$app ){
            return error( 'APP_NOT_EXIST' );
        }

        sActionLog::init( 'DELETE_APP', $app );
        $app->delete_apps( $uid );
        sActionLog::save( $app );

        return $app;
    }

    public static function getAppList($name = '', $status = mApp::STATUS_NORMAL ){
        $app = new mApp();
        $apps = $app->get_apps($name, $status);

        return $apps;
        #return self::brief( $apps );
    }

    public static function sortApps($sorts) {
        $appModel = new mApp();
        foreach ($sorts as $order => $id) {
            $app = $appModel->get_app_by_id($id);
            if( !$app ) {
                return error('APP_NOT_EXIST');
            }
            $app->order_by = $order+1;
            $app->save();
        }

        return true;
    }

    public static function brief( $apps ){
        $apps['logo_url'] = get_cloudcdn_url( $v->savename );
        return $apps;
    }

    public static function shareApp( $share_type, $target_type, $target_id, $width = 320 ){
        $data = array();
        $mobile_host = env('API_HOST');

        /*
            $url  = 'http://'.$mobile_host."/ask/share/".$item['ask_id'];
            $rlt = self::http_get( 'http://'.$mobile_host.':8808/?url='.$url );
            if( $rlt ){
                $rlt = json_decode($rlt);
                $data['image'] = 'http://'.$mobile_host.'/images/'.$rlt->image_url;
            }
         */

        $is_tutorial = false;
        $is_homework = false;
        $data['type'] = 'url';
        if ( $target_type == mLabel::TYPE_ASK )  {
            $item = sAsk::getAskById($target_id); //$item = sAsk::brief($item);
            $is_tutorial = sThreadCategory::checkedThreadAsCategoryType( $target_type, $target_id, mThreadCategory::CATEGORY_TYPE_TUTORIAL );
            $uploads = sUpload::getUploadByIds(explode(',', $item->upload_ids));
            $data['image'] = CloudCDN::file_url($uploads[0]->savename, 100);
        }
        else {
            $item = sReply::getReplyById($target_id); //$item = sReply::brief($item);
            $is_homework = sThreadCategory::checkedThreadAsCategoryType( mLabel::TYPE_REPLY, $target_id, mThreadCategory::CATEGORY_TYPE_TUTORIAL );
            $upload = sUpload::getUploadById($item->upload_id);
            $data['image'] = CloudCDN::file_url($upload->savename, 100);
        }
        $user = sUser::getUserByUid($item->uid);

        $data['url']    = "http://$mobile_host/app/page?type=$target_type&&id=$target_id";
        $data['title']  = $data['desc'] = $item->desc;
        if( $is_tutorial ){
            $description = json_decode( $item->desc, true );
            $data['title'] = $description['title'];
            $data['desc']  = $description['description'];
        }

        $share_count_type = '';
        switch($share_type) {
        case 'wechat_timeline':
            $share_count_type = 'timeline_share';
            if($target_type == mLabel::TYPE_ASK) {
                if( $is_tutorial ){
                    $data['url'] = 'http://'.env('API_HOST').'/sharecourse/cn/shareCourse.html?tutorial_id='.$target_id;
                    $data['title'] = '我分享了一个'.$data['title'].'的教程。#图派';
                }
                else{
                    $data['title'] = '我分享了一张“'.$user->nickname.'”的图片，速度求P！#图派';
                }
            }
            else {
                if( $is_homework ){
                    $data['url'] = 'http://'.env('API_HOST').'/sharecourse/cn/task.html?tutorial_id='.$item->ask_id.'&reply_id'.$target_id;
                    $data['title'] = '我分享了一张'.$data['title'].'的作业。#图派';
                }
                else{
                    $data['title'] = '我分享了“'.$user->nickname.'”贼酷炫的作品，#图派#大神名不虚传！';
                }
            }
            $data['type'] = 'url';
            break;
        case 'wechat':
        case 'wechat_friend':
            $share_count_type = 'weixin_share';
            $data['type'] = 'url';
            if($target_type == mLabel::TYPE_ASK) {
                if( $is_tutorial ){
                    $data['title'] = '我分享了一个'.$data['title'].'的教程。#图派';
                }
                else{
                    $data['title'] = '我分享了一张“'.$user->nickname.'”的图片，速度求P！#图派';
                }
            }
            else {
                $data['title'] = '我分享了“'.$user->nickname.'”贼酷炫的作品，#图派#大神名不虚传！';
            }
            break;
        case 'qq_timeline':
        case 'qq_friend':
            if($target_type == mLabel::TYPE_ASK) {
                if( $is_tutorial ){
                    $data['title'] = '我分享了一个'.$data['title'].'的教程。#图派';
                }
                else{
                     $data['title'] = '我分享了一张“'.$user->nickname.'”的照片，速度求P';
                    $data['desc']  = '#图派';
                }
            }
            else {
                $data['title'] = '我分享了一张“'.$user->nickname.'”的照片，大神太腻害，膜拜之！';
                $data['desc']  = '#图派';
            }
            break;
        case 'weibo':
            $data['type'] = 'image';
            if($target_type == mLabel::TYPE_ASK) {
                //$data['desc']  = ' #我在图派求P图#从@图派tupai分享，围观下“'.$data['url'].' H5链接”';
                if( $is_tutorial ){
                    $data['title'] = '我分享了一个'.$data['title'].'的教程。#图派';
                }
                else{
                    $data['desc'] = '#我在图派求P图# 从@图派App 分享，围观下'.$data['url'];
                }
            }
            else {
                //$data['desc']  = ' 大神真厉害，膜拜之！#图派大神#从@图派tupai分享，围观下“'.$data['url'].' H5链接”';
                $data['desc'] = '#图派大神#太厉害，膜拜之！从@图派App 分享，围观下'.$data['url'];
            }
            break;
        case 'copy':
            $data['type'] = 'copy';
            break;
        }


        if( $target_type == mLabel::TYPE_ASK ){
            sAsk::shareAsk($target_id, mCount::STATUS_NORMAL);
            //sAsk::updateAskCount( $target_id, 'share', mCount::STATUS_NORMAL );
            if( $share_count_type ){
                cAskTimelineShares::inc($target_id);
                // sAsk::updateAskCount( $target_id, $share_count_type, mCount::STATUS_NORMAL );
            }
        }
        else if( $target_type == mLabel::TYPE_REPLY ){
            sReply::shareReply($target_id, mCount::STATUS_NORMAL);
            //sReply::updateReplyCount( $target_id, 'share', mCount::STATUS_NORMAL );
            if( $share_count_type ){
                sReply::updateReplyCount( $target_id, $share_count_type, mCount::STATUS_NORMAL );
            }
        }
        return $data;
    }

    private static function http_get( $url ){
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        ob_start();
        curl_exec( $ch );
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}
