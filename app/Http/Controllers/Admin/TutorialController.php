<?php namespace App\Http\Controllers\Admin;

use App\Models\Ask as mAsk;
use App\Models\User as mUser;
use App\Models\Label as mLabel;
use App\Models\Category as mCategory;

use App\Services\Ask as sAsk;
use App\Services\User as sUser;
use App\Services\Upload as sUpload;
use App\Services\ThreadCategory as sThreadCategory;

use App\Counters\AskClicks as cAskClicks;
use App\Counters\AskComments as cAskComments;
use App\Counters\AskDownloads as cAskDownloads;
use App\Counters\AskReplies as cAskReplies;
use App\Counters\AskInforms as cAskInforms;
use App\Counters\AskShares as cAskShares;


use App\Facades\CloudCDN;
use Html;

class TutorialController extends ControllerBase {
	public function indexAction(){
		return $this->output_html();
	}

    public function list_tutorialsAction(){
        $page = $this->post( 'page', 'int', 1 );
        $size = $this->post( 'size', 'int', 15 );
        $data = sThreadCategory::getAsksByCategoryId( mCategory::CATEGORY_TYPE_TUTORIAL, [mCategory::STATUS_NORMAL,mCategory::STATUS_READY,mCategory::STATUS_HIDDEN] ,$page, $size, mAsk::STATUS_NORMAL );
        $tutorials = [];
        $pc_host = env('MAIN_HOST');
        foreach ($data as $row) {
            $tutorial = sAsk::tutorialDetail( sAsk::getAskById( $row->target_id ) );

            $tutorial_id = $tutorial['id'];
            $link = 'http://'.$pc_host.'/htmls/tutorials_'.$tutorial['id'].'.html';
            $tutorial['link'] = '<a href="'.$link.'">教程链接</a>';
            $tutorial['cover'] = '<img src="'.CloudCDN::file_url( $tutorial['image_url'] ).'" data-id="'.$tutorial['upload_id'].'" />';

            $oper = [];
            if(    $row->status != mCategory::STATUS_DONE
                && $row->status != mCategory::STATUS_DELETED
            ){
                $oper[] = "<a href='#' data-toggle='modal' data-id='$tutorial_id' class='edit'>编辑</a>";
            }

            if( $row->status == mCategory::STATUS_DELETED ){
                $oper[] = "<a href='#' data-status='restore' data-id='".$tutorial_id."' class='restore'>恢复</a>";
            }
            else if( $row->status != mCategory::STATUS_NORMAL) {
                $oper[] = "<a href='#' data-id='$tutorial_id' data-status='delete' class='delete'>删除</a>";
            }

            if( $row->status == mCategory::STATUS_NORMAL ){
                $oper[] = "<a href='#' data-id='".$tutorial_id."' data-status='hide' class='offline'>失效</a>";
            }
            if( $row->status == mCategory::STATUS_READY
                || $row->status == mCategory::STATUS_HIDDEN ){
                $oper[] = "<a href='#' data-id='".$tutorial_id."' data-status='online' class='online'>生效</a>";
            }

            $tutorial['oper'] = implode( ' / ', $oper );
            $tutorials[] = $tutorial;
        }

        return $this->output_json( $tutorials );
    }

    public function list_usersAction(){
        $users = sUser::getValidUsers();
        return $this->output_json(['users'=> $users]);
    }

    public function get_tutorial_pics_by_idAction(){
        $id = $this->get( 'id', 'int' );
        $ask = sAsk::detail( sAsk::getAskById( $id ) );

        $ask_uploads = $ask['ask_uploads'];
        $upload_ids = explode( ',', $ask['upload_id'] );

        $tutorial_imgs = [];
        foreach ($ask_uploads as $key => $value) {
            $tutorial_img = [];
            $tutorial_img['id'] = $upload_ids[$key];
            $tutorial_img['url'] = $value['image_url'];
            $tutorial_imgs[] = $tutorial_img;
        }

        return $this->output_json( $tutorial_imgs );
    }

    public function set_tutorialAction(){
        $tutorial_id = $this->post('tutorial_id', 'int' );
        $uid = $this->post('uid', 'int', $this->_uid );
        $title = $this->post('title', 'string' );
        $description = $this->post( 'description', 'string' );
        $cover_ids = $this->post( 'cover_ids', 'int',[] );
        $status = $this->post( 'status', 'int', mAsk::STATUS_NORMAL );

        if( !$title ){
            return error('EMPTY_TITLE');
        }
        if( !array_filter($cover_ids) ){
            return error('EMPTY_UPLOAD_ID');
        }

        if($tutorial_id) {
            $ask = sAsk::getAskById($tutorial_id);
        }
        else {
            $ask = new mAsk;
        }
        $desc = json_encode( [
            'title' => $title,
            'description' => $description
        ]);

        $ask->uid = $uid;
        $ask->upload_ids= implode(',', $cover_ids );
        $ask->desc      = $desc;
        $ask->status    = $status;
        $ask->save();


        if( !$tutorial_id ) {
            sThreadCategory::addCategoryToThread( $this->_uid, mAsk::TYPE_ASK, $ask->id, mCategory::CATEGORY_TYPE_TUTORIAL, mCategory::STATUS_NORMAL);
        }

        return $this->output( ['id'=>$ask->id, 'result'=>'ok'] );
    }

    public function update_statusAction(){
        $id = $this->post( 'id', 'int' );
        $status_name = $this->post( 'status', 'string' );

        if( !$id ){
            return error('EMPTY_CATEGORY_ID');
        }

        switch( $status_name ){
            case 'offline':
                $status = mCategory::STATUS_DONE;
                break;
            case 'online':
                $status = mCategory::STATUS_NORMAL;
                break;
            case 'delete':
                $status = mCategory::STATUS_DELETED;
                break;
            case 'restore':  //回复
                $status = mCategory::STATUS_HIDDEN;
                break;
            case 'hide':
                $status = mCategory::STATUS_READY;
                break;
            default:
                return error('EMPTY_STATUS');
        }

        sThreadCategory::setThreadStatus( $this->_uid, mCategory::TYPE_ASK, $id, $status );

        return $this->output_json(['result' => 'ok']);
    }
}
