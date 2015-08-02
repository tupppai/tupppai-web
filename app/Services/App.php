<?php
namespace App\Services;

use App\Models\App as mApp,
    App\Models\Ask as mAsk,
    App\Models\Reply as mReply,
    App\Models\Label as mLabel,
    App\Models\Upload as mUpload;

use App\Services\Label as sLabel,
    App\Services\ActionLog as sActionLog;

class App extends ServiceBase{

    public static function addNewApp( $app_name, $logo_id, $jump_url ){
        if( !filter_var( $jump_url, FILTER_CALLBACK, array( 'options' => 'match_url_format' ) ) )
            return error( 'ERROR_URL_FORMAT' );

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
        #return self::brief( $app );
    }

    public static function delApp( $app_id ){
        $app = new mApp();
        $app = $app::findFirst( $app_id );
        if( !$app )
            return error( 'EMPTY_APP' );
        sActionLog::init( 'DELETE_APP', $app );

        $app->assign(array(
            'del_by'    => _uid(),
            'del_time'  => time()
        ));
        $app->save();
        sActionLog::save( $app );

        return self::brief( $app );
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
    public static function getAppList(){
        $app = new mApp();
        $apps = $app->get_apps();

        return self::brief( $apps );
    }

    //todo: simplify
    public static function shareApp( $target_type, $target_id, $width = 320 ){
        $data = array();
        $config = read_config('configs');
        $mobile_host = $config['host']['mobile'];

        if( $target_type == mLabel::TYPE_ASK ){
            $ask = new mAsk();
            $item = $ask->get_ask_by_id( $target_id );
        }
        else if( $target_type == mLabel::TYPE_REPLY ){
            $reply = new mReply();
            $item = $reply->getReplyById( $target_id );

            $data['type'] = 'image';
            $url  = 'http://'.$mobile_host."/ask/share/".$item->ask_id;

            $rlt = self::http_get( 'http://'.$mobile_host.':8808/?url='.$url );
            if( $rlt ){
                $rlt = json_decode($rlt);
                $data['image'] = 'http://'.$mobile_host.'/images/'.$rlt->image_url;
            }
            else {
                $data['type']   = 'url';
                $image = $item->upload->resize( $width );
                $data['image']  = $image['image_url'];
            }
        }

        $data['url']  = 'http://'.$mobile_host."/".$item->id;
        $labels = sLabel::getLabels( $target_type, $target_id, 1, 1000 );
        $content = array_column( $labels, 'content' );
        $data['title'] = $data['desc'] = implode( ',', $content );
        return $data;
    }

    private function http_get( $url ){
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        ob_start();
        curl_exec( $ch );
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}
