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
        $data = sThreadCategory::getAsksByCategoryId( mCategory::CATEGORY_TYPE_TUTORIAL, mCategory::STATUS_NORMAL ,$page, $size, mAsk::STATUS_NORMAL );
        $tutorials = [];
        $pc_host = env('MAIN_HOST');
        foreach ($data as $row) {
            $tutorial = sAsk::detail( sAsk::getAskById( $row->target_id ) );

            $tutorial_id = $tutorial['id'];
            $link = 'http://'.$pc_host.'/htmls/tutorials_'.$tutorial['id'].'.html';
            $tutorial['link'] = '<a href="'.$link.'">教程链接</a>';
            $tutorial['cover'] = '<img src="'.CloudCDN::file_url( $tutorial['image_url'] ).'" data-id="'.$tutorial['upload_id'].'" />';
            $content = json_decode($tutorial['desc'], true);
            $tutorial['title'] = $content['title'];
            $tutorial['description'] = $content['description'];

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
                $oper[] = "<a href='#' data-id='".$tutorial_id."' data-status='undelete' class='offline'>失效</a>";
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

    public function save_tutorial_htmlAction(){
        $tutorial_id = $this->post( 'tutorial_id', 'int' );
        $content = $this->post( 'content', 'string' );
        if( !$tutorial_id ){
            return error('WRONG_ARGUMENTS', '教程id不能为空');
        }

        if( !$content ){
            return error('WRONG_ARGUMENTS', '内容不能为空');
        }

        $path = base_path().'/public/htmls/tutorials_'.$tutorial_id.".html";
        file_put_contents($path, $content);

        return $this->output();
    }

    public function set_tutorialAction(){
        $tutorial_id = $this->post('tutorial_id', 'int' );
        $uid = $this->post('uid', 'int', $this->_uid );
        $title = $this->post('title', 'string' );
        $description = $this->post( 'description', 'string' );
        $cover_id = $this->post( 'cover_id', 'int' );
        $status = $this->post( 'status', 'int', mAsk::STATUS_NORMAL );

        if( !$title ){
            return error('EMPTY_TITLE');
        }

        if($tutorial_id) {
            $ask = sAsk::getAskById($tutorial_id);
        }
        else {
            if( !$cover_id ){
                return error('EMPTY_UPLOAD_ID');
            }
            $ask = new mAsk;
        }
        $desc = json_encode( [
            'title' => $title,
            'description' => $description
        ]);

        $ask->uid = $uid;
        $ask->upload_ids= $cover_id;
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
        $status = $this->post( 'status', 'int' );

        return $this->output_json(['result' => 'ok']);
    }
}
