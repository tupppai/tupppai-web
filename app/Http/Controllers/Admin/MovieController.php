<?php namespace App\Http\Controllers\Admin;

use Storage;
use Request;
use Validator;
use App\Services\Upload as sUpload;
use App\Models\Upload as mUpload;

use Qiniu\Auth;

class MovieController extends ControllerBase{
	public function indexAction(){
        return $this->output();
	}
    public function listAction(){
        $movies = mUpload::where('options', 'movie')->get();
        foreach( $movies as $movie ){
            $movie->url = $this->genDLLink( $movie->savename );
        }
        return $this->output_json( $movies );
    }

    protected function genDLLink( $name ){
        $domain = env('QINIU_MOVIE_DOMAIN');
        $ak = env('QINIU_MOVIE_AK');
        $sk = env('QINIU_MOVIE_SK');

        $url = 'http://'.$domain.'/'.$name;
        $auth = new Auth( $ak, $sk );
        $realDLurl = $auth->privateDownloadUrl( $url, 3600);
        return $realDLurl;
    }

    public function uploadAction(){
        if (empty($file = Request::file())) {
            return error('FILE_NOT_EXIST');
        }
        $rules = array();
        $validator = Validator::make($file, $rules);

        if ($validator->fails()) {
            return error('FILE_NOT_VALID');
        }
        $file = $file['Filedata'];
        $size = $file->getSize();

        $save_name  = self::generate_filename_by_file($file->getClientOriginalName());
		$disk = Storage::disk('qiniu');
		$ret = $disk->put($save_name, file_get_contents($file->getPathName()) );

        $upload = sUpload::addNewUpload(
            $file->getClientOriginalName(),
            $save_name,
            $ret,
            1,
            1,
            $size,
            'qiniu',
            'movie'
        );

        return array(
            'id'=>$upload->id
        );
    }
    protected function generate_filename_by_file($filename){
        $ext = $this->get_ext($filename);
        return $this->gen_name() . '.' . $ext;
    }

    private function gen_name($prefix=''){
        return date('Ymd-His') . uniqid($prefix);
    }

    private function get_ext($file_name) {
        $tmp = explode(".", $file_name);
        if(sizeof($tmp) <= 1){
            $ext = "jpg";
        }
        else {
            $ext = end($tmp);
        }
        return $ext;
    }

	public function viewAction(){

	}
}
