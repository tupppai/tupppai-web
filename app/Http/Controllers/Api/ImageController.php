<?php namespace App\Http\Controllers\Api;

use App\Models\Upload as mUpload,
    App\Models\User as mUser,
    App\Models\UserLanding as mUserLanding,
    App\Models\Download as mDownload;

use App\Facades\CloudCDN;
use App\Jobs\Push, Queue;

class ImageController extends ControllerBase
{
    public $_allow = array(
        'upload'
    );

    public function testAction() {
        echo '
            <form action="upload" method="post"
            enctype="multipart/form-data">
            <label for="file">Filename:</label>
            <input type="file" name="file2" id="file" /> 
            <br />
            <input type="submit" name="submit" value="Submit" />
            </form>
            ';
    }

    public function uploadAction() {
        $data = $this->_upload_cloudCDN();

        return $this->output($data);
    }

    use \App\Traits\UploadImage; 
}
