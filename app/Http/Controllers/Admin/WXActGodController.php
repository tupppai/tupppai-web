<?php namespace App\Http\Controllers\Admin;

use App\Models\ThreadCategory as mThreadCategory;
use App\Models\Ask as mAsk;
use App\Models\Role as mRole;
use App\Models\Reply as mReply;
use App\Models\Category as mCategory;

use App\Services\User as sUser;
use App\Services\UserRole as sUserRole;
use App\Services\Ask as sAsk;
use App\Services\Askmeta as sAskmeta;
use App\Services\Reply as sReply;
use App\Services\Upload as sUpload;
use App\Services\ThreadCategory as sThreadCategory;
use App\Services\Category as sCategory;
use App\Services\Download as sDownload;

use Request;
use DB;

class WXActGodController extends ControllerBase{
    protected $category;
    const ASSIGN_RECORD_META_NAME = 'WXActGod_assign_records';
    const ASSIGN_UID_META_NAME = 'WXActGod_assign_uid';
    public function __construct( Request $request ){
        parent::__construct( $request );

        $category = sCategory::getCategoryByName('WXActGod');
        if(!$category){
            return error('CATEGORY_NOT_EXIST', '活动不存在');
        }

        $this->category = $category;
    }
    public function indexAction(){
        $category = $this->category;
        $oper = [];
        if( $category->status == mCategory::STATUS_NORMAL ){
            $oper[] = "<a href='#' data-id='".$category->id."' data-status='undelete' class='undelete'>临时关闭活动</a>";
            $oper[] = "<a href='#' data-id='".$category->id."' data-status='offline' class='offline'>下架</a>";
        }
        else if( $category->status == mCategory::STATUS_READY
            || $category->status == mCategory::STATUS_HIDDEN ){
            $oper[] = "<a href='#' data-id='".$category->id."' data-status='online' class='online'>上架</a>";
        }

        return $this->output( ['oper' => implode('/',$oper)] );
    }

    public function worksAction(){
        return $this->output();
    }

    public function list_requestsAction(){
        $page = $this->get('page', 'int', 1 );
        $size = $this->get('size', 'int', 15 );
        $request_status = $this->get( 'ask_status', 'string', NULL);
        switch( $request_status ){
            case 'pending':
                $request_status = [mAsk::STATUS_NORMAL];
                break;
            case 'processing':
                $request_status = [mAsk::STATUS_HIDDEN];
                break;
            case 'rejected':
                $request_status = [mAsk::STATUS_REJECT];
                break;
            case 'done':
                $request_status = [mAsk::STATUS_DONE];
                break;
            default:
                $request_status = [ mAsk::STATUS_NORMAL, mAsk::STATUS_HIDDEN, mAsk::STATUS_REJECT, mAsk::STATUS_DONE ];
                break;
        }
        $operator = $this->get( 'assign_uid', 'int', NULL);
        $reply_status = $this->get( 'reply_status', 'string', NULL );
        $category_id = $this->category->id;

        $query = (new mAsk)->leftjoin('thread_categories', function($table ) use ( $category_id ){
                $table->on('thread_categories.target_id', '=', 'asks.id');
            })
            ->where('thread_categories.category_id','=', $category_id )
            ->leftjoin('replies', function( $table ){
                $table->on('replies.ask_id','=', 'asks.id');
            })
            ->leftjoin('downloads', function( $table ) use ($category_id){
                $table->on('downloads.target_id', '=', 'replies.id')
                    ->on('downloads.uid','=','asks.uid')
                    ->where( 'downloads.type', '=', mAsk::TYPE_REPLY)
                    ->where('downloads.category_id', '=', $category_id);
            })
            ->leftjoin('askmeta', function( $table ){
                $table->on('askmeta.ask_id', '=', 'asks.id')
                        ->where('ameta_key', '=', self::ASSIGN_UID_META_NAME);
            })
            ->whereIn( 'asks.status', $request_status )
            ->groupBy('asks.id')
            ->orderBy('asks.create_time', 'DESC')
            ->orderBy('replies.create_time', 'DESC')
            ->orderBy('downloads.id', 'DESC')
            ->selectRaw( 'asks.id as target_id, replies.id as reply_id, max(replies.ask_id), downloads.id as download_id, askmeta.ameta_value as operator_uid');

        if( $reply_status ){
            $not = '';
            if( $reply_status == 'received'){
                $not = 'not ';
            }
            $having = 'download_id is '.$not.'null';
            $query = $query->havingRaw( $having );
        }
        if( $operator ){
            $query = $query->where('askmeta.ameta_value', '=', $operator);
        }

        $total = DB::table( DB::raw('('.$query->toSql().') as sub') )
            ->mergeBindings($query->getQuery())
            ->count();

        $asks = $query ->forPage( $page, $size )->get();

        foreach($asks as $ask){
            $ask_id = $ask->target_id;

            $req_ask = sAsk::getAskById( $ask_id );
            $request_ask = sAsk::detail( $req_ask );
            $ask->id = $ask->target_id;
            $ask->uid = $request_ask['uid'];
            $ask->create_time = date('Y-m-d H:i:s', $request_ask['create_time']);
            $ask->request = $request_ask['desc'];
            $ask->request_image = '<img src="'.$request_ask['image_url'].'" /><a target="_blank" href="'.preg_replace('/\?.*/', '', $request_ask['image_url']).'">下载原图</a>';

            $oper = [];
            $ask->request_status = '';
            switch( $req_ask->status ){
                case mAsk::STATUS_HIDDEN:
                    $ask->request_status = '进行中';
                    $oper[] = '<a href="#upload-modal" data-toggle="modal">上传作品</a>';
                    $oper[] = '<a href="#assign-modal" data-toggle="modal">修改设计师</a>';
                    break;
                case mAsk::STATUS_REJECT:
                    $ask->request_status = '已拒绝';
                    break;
                case mAsk::STATUS_DONE:
                    $ask->request_status = '已完成';
                    $oper[] = '<a href="#upload-modal" data-toggle="modal">上传作品</a>';
                    break;
                case mAsk::STATUS_READY:
                default:
                    $ask->request_status = '未处理';
                    $oper[] = '<a href="#assign-modal" data-toggle="modal">领取</a>';
                    $oper[] = '<a href="#reject-modal" data-toggle="modal">拒绝</a>';
                    break;
            }
            if( $ask->operator_uid ){
                $user = sUser::getUserByUid($ask->operator_uid);
                $ask->request_status .= '<br />设计师：'.$user->nickname;
            }
            $ask->oper = implode('/', $oper);

            $ask->received_status = '未领取';
            $ask->reply_image = '无';
            $response_reply = sReply::detail( sReply::getReplyById( $ask->reply_id ));
            if( $response_reply ){
                $ask->reply_image = '<img src="'.$response_reply['image_url'].'" /><a target="_blank" href="'.preg_replace('/\?.*/', '', $response_reply['image_url']).'">下载原图</a>';

                if( $ask->download_id ){
                    $ask->received_status = '已领取';
                }
            }
        }
        $data = [];
        $data['data'] = $asks->toArray();
        $data['recordsTotal'] = $total;
        $data['recordsFiltered'] = $total;
        // 输出json
        return $this->output_table($data);
    }

    public function get_designersAction(){
        $type = $this->post('type', 'string', '' );
        $roles = [];
        switch( $type){
            case 'comment':
                $roles = [ mRole::ROLE_CRITIC ];
                break;
            case 'puppets':
                $roles = [
                    mRole::ROLE_HELP,
                    mRole::ROLE_WORK,
                    mRole::ROLE_CRITIC
                ];
                break;
            case 'staff':
                $roles = [
                    mRole::ROLE_STAFF
                ];
                break;
            default:
                $roles = [];
                break;
        }

        $userIds = sUserRole::getUidsByIds( $roles );
        $users = [];
        foreach( $userIds as $userId ){
            $users[] = sUser::getUserByUid( $userId );
        }
        return $this->output_json( $users );
    }
    public function update_statusAction(){
        $status = $this->post('status', 'string' );

        if( is_null($status) ){
            return error('EMPTY_STATUS');
        }

        $activity = sCategory::updateStatus( $this->category->id, $status );

        return $this->output( ['id'=>$activity->id] );
    }

    public function set_jobAction(){
        $ask_id = $this->post('target_id', 'int');
        $uid = $this->post('assign_uid', 'int', NULL);
        $reason = $this->post('reason', 'string', NULL);

        if( !$ask_id ){
            return error('EMPTY_TARGET_ID', '请选择分配的id');
        }

        $ask = sAsk::getAskById( $ask_id );
        if( !$ask ){
            return error('ASK_NOT_EXIST','求助不存在');
        }

        $status = 'reject';
        if( is_null($reason) ){
            if( !$uid ){
                return error('EMPTY_UID', '请选择用户id');
            }
            $old_assign_uid = sAskmeta::get( $ask_id, self::ASSIGN_UID_META_NAME, NULL);
            if( is_null( $old_assign_uid ) ){
                $status = 'assign';
            }
            else{
                $status = 'modify';
            }
            sAskmeta::set( $ask_id, self::ASSIGN_UID_META_NAME, $uid );
            sAsk::updateAskStatus( $ask, mAsk::STATUS_HIDDEN, $this->_uid );
        }
        else{
            sAskmeta::set( $ask_id, self::ASSIGN_UID_META_NAME, NULL );
            sAsk::updateAskStatus( $ask, mAsk::STATUS_REJECT, $this->_uid );
        }

        $assignment = [];
        $assignment['oper_by'] = $this->_uid;
        $assignment['oper_time'] = time();
        $assignment['assign_user'] = $uid;
        $assignment['assign_status'] = $status;
        $assignment['reason'] = $reason;

        $records = sAskmeta::get( $ask_id, self::ASSIGN_RECORD_META_NAME, json_encode( [] ) );
        $records = json_decode( $records );
        array_unshift( $records, json_encode( $assignment ) );
        sAskmeta::set( $ask_id, self::ASSIGN_RECORD_META_NAME, json_encode( $records ) );

        return $this->output_json(['result'=>'ok']);
    }

    public function upload_responseAction( ){
        $uid        = $this->_uid;
        $ask_id     = $this->post('target_id', 'int', 0);
        $upload_ids = $this->post('upload_ids', 'json_array' );
        $ratios     = $this->post(
            'ratios',
            'json_array',
            config('global.app.DEFAULT_RATIO')
        );
        $scales     = $this->post(
            'scale',
            'json_array',
            config('global.app.DEFAULT_SCALE')
        );
        $desc       = $this->post( 'desc', 'string', '' );
        $category_id= $this->category->id;

        if( !$upload_ids || empty($upload_ids) ) {
            return error('EMPTY_UPLOAD_ID');
        }

        $ask = sAsk::getAskById( $ask_id );
        if( !$ask ){
            return error('ASK_NOT_EXIST','求助不存在');
        }

        //还是单张图片的求助
        $reply  = sReply::addNewReply( $uid, $ask_id, $upload_ids[0], $desc, $category_id);
        $upload = sUpload::updateImages( $upload_ids, $scales, $ratios );

        sAsk::updateAskStatus( $ask, mAsk::STATUS_DONE, $this->_uid );
        return $this->output([
            'id' => $reply->id,
            'ask_id' => $ask_id,
            'category_id' => $category_id,
            'result' => 'ok'
        ]);
    }
}
