<?php
namespace App\Services;

use App\Models\App as mApp,
    App\Models\Ask as mAsk,
    App\Models\Reply as mReply,
    App\Models\Label as mLabel,
    App\Models\Upload as mUpload;

use App\Services\Label as sLabel,
    App\Services\Reply as sReply,
    App\Services\Ask as sAsk,
    App\Services\ActionLog as sActionLog;

class App extends ServiceBase{

    public static function addNewApp( $app_name, $logo_id, $jump_url ){
        $app = new mApp();
        sActionLog::init( 'ADD_APP', $app );
        $app->assign(array(
            'app_name'       => $app_name,
            'logo_upload_id' => $logo_id,
            'jumpurl'        => $jump_url,
            'create_by'      => $uid
        ));
        $app->save();
        sActionLog::save( $app );

        return $app;
    }

    public static function delApp( $uid, $app_id ){
        $mApp= new mApp();
        $app = $mApp->get_app_by_id($id);
        if( !$app )
            return error( 'EMPTY_APP' );
        sActionLog::init( 'DELETE_APP', $app );

        $app->assign(array(
            'del_by'    => $uid,
            'del_time'  => time()
        ));
        $app->save();
        sActionLog::save( $app );

        return self::brief( $app );
    }
    
    public static function getAppList(){
        $app = new mApp();
        $apps = $app->get_apps();

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
        $app_list = array();
        foreach( $apps as $k => $v ){
            $app_list[$k] = array();
            $app_list[$k]['app_name'] = $v->app_name;
            $app_list[$k]['jump_url'] = $v->jumpurl;
            $app_list[$k]['logo_url'] = get_cloudcdn_url( $v->savename );
        }
        return $app_list;
    } 

    //todo: simplify
    public static function shareApp( $target_type, $target_id, $width = 320 ){
        $data = array();
        $mobile_host = env('MOBILE_HOST');

        $data['type'] = 'image';
        if( $target_type == mLabel::TYPE_ASK ){
            $item = sAsk::detail(sAsk::getAskById( $target_id ));
            $data['image'] = $item['image_url'];
        }
        else if( $target_type == mLabel::TYPE_REPLY ){
            $reply = new mReply();
            $item = sReply::detail( $reply->get_reply_by_id( $target_id ) );

            $url  = 'http://'.$mobile_host."/ask/share/".$item['ask_id'];

            $rlt = self::http_get( 'http://'.$mobile_host.':8808/?url='.$url );
            if( $rlt ){
                $rlt = json_decode($rlt);
                $data['image'] = 'http://'.$mobile_host.'/images/'.$rlt->image_url;
            }
            else {
                $data['type']   = 'url';
                $data['image']  = $item['image_url'];
            }
        }

        $data['url']  = 'http://'.$mobile_host."/".$item['id'];
        $labels = sLabel::getLabels( $target_type, $target_id, 1, 1000 );
        $content = array_column( $labels, 'content' );
        $data['title'] = $data['desc'] = implode( ',', $content );
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
