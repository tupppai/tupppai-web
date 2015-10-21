<?php
namespace App\Http\Controllers\Admin;
use App\Services\Puppet as sPuppet;
use App\Services\ActionLog as sActionLog;
use App\Services\Upload as sUpload;
use App\Facades\CloudCDN;


class PuppetController extends ControllerBase{
    public function indexAction(){
        return $this->output();
    }
    
    public function batchAction(){
        return $this->output();
    }

    public function asksAction(){
        $uid = $this->_uid;
        $puppets = sPuppet::getPuppets( $uid, [1,2] );

        return $this->output( compact('puppets') );
    }

    public function list_puppetsAction(){
        $cond = array();
        $nickname = $this->post("nickname", "string");
        $uid = $this->post("uid", "string");

        if( $nickname ){
            $cond['nickname']   = $nickname;
        }
        if( $uid ){
            $cond['uid']   = $uid;
        }

        $uid = $this->_uid;
        $data = sPuppet::getPuppetList( $this->_uid, $cond );

        return $this->output_table( $data );
    }

    public function get_puppetsAction(){
        $puppets = sPuppet::getPuppets( $this->_uid );

        return $this->output_json( $puppets );
    }

    public function edit_profileAction(){
        $uid = $this->post( 'uid', 'int', 0 );
        $nickname = $this->post( 'nickname', 'string' );
        $gender = $this->post( 'sex', 'int' );
        $avatar = $this->post( 'avatar', 'string' );
        $phone = $this->post( 'phone', 'string' );
        $roles = $this->post( 'roles', 'array',[] );
        if( !$nickname ){
            return error( 'EMPTY_NICKNAME', '请输入昵称' );
        }
        if( is_null( $gender ) ){
            return error( 'EMPTY_SEX' ,'请选择性别' );
        }
        if( !$avatar ){
            return error( 'EMPTY_AVATAR', '请上传头像' );
        }
        $data = [
            'nickname' => $nickname,
            'username' => $nickname,
            'sex' => $gender,
            'avatar' => $avatar,
            'password' => '',
            'phone' => $phone,
            'roles' => $roles
        ];

        $user = sPuppet::editProfile( $this->_uid, $uid, $data );
        $rel = sPuppet::updatePuppetRelationOf( $this->_uid, $user->uid );

        return $this->output( ['result' => 'ok'] );
    }

    /**
     * 批量上传文件，文件格式zip，文件名即求助内容
     */
    public function uploadAction()
    {
        if ($_FILES["file"]["error"] > 0) {
            pr($_FILES["file"]["error"]);
        }
        if(!env('dev')) {
            $type = $_FILES["file"]["type"];
            if($type != "application/octet-stream" and $type != "application/zip"){
                pr("zip only");
            }
        }
        $tmp = storage_path('zips/');
        if (!file_exists($tmp)) {
            mkdir($tmp, 0777, true);
        }

        $file_path = $tmp.md5(time().$_FILES["file"]["name"]).".zip";
        move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);

        $uploads = array();
        $zip = zip_open($file_path);
        if ($zip)
        {
            while ($zip_entry = zip_read($zip))
            {
                if (zip_entry_open($zip, $zip_entry))
                {
                    $file_name  = zip_entry_name($zip_entry);
                    $encode     = mb_detect_encoding($file_name, "auto");
                    if($encode != 'UTF-8') {
                        $file_name = iconv('gbk', 'UTF-8', $file_name);
                    }
                    $contents = "";
                    while($row = zip_entry_read($zip_entry)){
                        $contents .= $row;
                    }
                    //get file name
                    if($contents == "" || sizeof(explode(".", $file_name)) == 1){
                        continue;
                    }
                    $savename  = CloudCDN::generate_filename_by_file($file_name);

                    $upload_dir = env('IMAGE_UPLOAD_DIR');
                    $upload_dir = $upload_dir . date("Ym")."/";
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $path = $upload_dir.$savename;
                    file_put_contents($path, $contents);
                    $size = getimagesize($path);
                    $ratio= $size[1]/$size[0];
                    $scale= 1;
                    $size = $size[1]*$size[0];

                    $ret = CloudCDN::upload($path, $savename);
                    if ($ret) {
                        $upload = sUpload::addNewUpload(
                            $file_name,
                            $savename,
                            $ret,
                            $ratio,
                            $scale,
                            $size,
                            'qiniu'
                        );

                        $gender   = substr($file_name, 0, 1);
                        $nickname = substr($file_name, 1);
                        $nickname = substr($nickname, 0, strrpos($nickname, '.'));
                        $avatar   = CloudCDN::file_url($savename);
                        $data = [
                            'nickname' => $nickname,
                            'username' => $nickname,
                            'sex' => $gender,
                            'avatar' => $avatar,
                            'password' => '',
                            'phone' => null,
                            'roles' => null 
                        ];

                        $user = sPuppet::editProfile( $this->_uid, 0, $data );
                        $rel  = sPuppet::updatePuppetRelationOf( $this->_uid, $user->uid );
                    }

                    zip_entry_close($zip_entry);
                }
            }
        }
        zip_close($zip);

        return redirect('/puppet/index');
    }
}
