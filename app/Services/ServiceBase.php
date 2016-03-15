<?php
namespace App\Services;

use App\Models\Label as mLabel;

use App\Services\Reply as sReply;
use App\Services\Ask as sAsk;
use App\Services\Comment as sComment;

class ServiceBase {
 
    public static function parse($type, $id) {
        switch( $type ){
        case mlabel::TYPE_REPLY:
            $reply = sReply::getReplyById($id) ;

            return sReply::detail( $reply );
        case mLabel::TYPE_ASK:
            $ask = sAsk::getAskById( $id );

            return sAsk::detail( $ask );
        case mLabel::TYPE_COMMENT:
            $comment = sComment::getCommentById($id);

            return sComment::detail($comment);
        }
    }

    public static function getImage($url,$save_dir='',$filename='',$type=0){
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }
        if(trim($save_dir)==''){
            $save_dir=storage_path('upload');
        }
        if(trim($filename)==''){//保存文件名
            $ext=strrchr($url,'.');
            $ext = '.jpg';
            if($ext!='.gif'&&$ext!='.jpg'){
                return array('file_name'=>'','save_path'=>'','error'=>3);
            }
            $filename=time().$ext;
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
    }   
}
