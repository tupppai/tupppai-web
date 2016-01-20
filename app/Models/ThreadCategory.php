<?php
namespace App\Models;

use App\Models\Follow as mFollow;
use Illuminate\Support\Facades\DB;

class ThreadCategory extends ModelBase{
    protected $table = 'thread_categories';
    protected $guarded = ['id'];

    public function scopeChecked( $query ){
        return $query->where($this->table.'.status', self::STATUS_CHECKED);
    }

    public function scopeHot( $query ){
        $hotCategoryId = self::CATEGORY_TYPE_POPULAR;
        return $query->where( 'category_id', $hotCategoryId );
    }

    public function set_category( $uid, $target_type, $target_id, $category_id = 0, $status = 0, $reason = '' ){
        $data = [
            'target_type' => $target_type,
            'target_id' => $target_id,
        ];
        if($category_id) $data['category_id'] = $category_id;

        $category = $this->firstOrNew( $data );

        if( !$category->id ) $data['create_by'] = $uid;

        $data['reason'] = $reason;
        $data['status'] = $status;
        $data['category_id'] = $category_id;
        $data['update_by'] = $uid;

        return $category->assign( $data )->save();
    }

    /**
     * 通过求助或者作品类型获取目录集合
     */
    public function get_category_ids_of_thread( $target_type, $target_id, $category_id = NULL, $status = NULL ){
        $cond = [
            'target_id' => $target_id,
            'target_type' => $target_type
        ];
        if( !is_null( $category_id ) ){
            $cond['category_id'] = $category_id;
        }
        if( !is_null( $status ) ){
            if( is_string( $status ) ){
                $cond['status'] = $status;
            }
        }
        $query = $this->where( $cond )
                ->orderBy('create_time', 'ASC' );
        if( is_array( $status ) ){
            $query = $query->whereIn( 'status', $status );
        }
        return $query->get();
    }

    public function get_valid_threads_by_category( $category_id, $page , $size, $orderByThread = false ,$searchArguments = []){
        $tcTable = $this->table;

        $users = mFollow::select('follow_who')
                    ->where( 'follows.status', '=', self::STATUS_BLOCKED )
                    ->where('follows.uid', '=', _uid())
                    ->get()->toArray();

        $searchArguments['users'] = $users;

        $result = $this->searchKeyword($searchArguments);

        //不需要搜索自然进入此条件 searchKeyword里面判断好了
        if(isset($result['query'])){
            $query = $result['query'];
            $query = $query->leftjoin('asks', function ($join) use ($tcTable, $users) {
                $join->on($tcTable . '.target_id', '=', 'asks.id')
                    ->where($tcTable . '.target_type', '=', 1);
                if (!empty($users)) {
                    $join->whereNotIn("asks.uid", $users);
                }
            })
                ->leftjoin('replies', function ($join) use ($tcTable, $users) {
                    $join->on($tcTable . '.target_id', '=', 'replies.id')
                        ->where($tcTable . '.target_type', '=', 2);
                    if (!empty($users)) {
                        $join->whereNotIn("replies.uid", $users);
                    }
                })
                ->where(function($query){
                    //$query->where(self::_blocking('replies'))
                    //->orWhere(self::_blocking('asks'));
                    $query->where(function($query){
                        $uid = _uid();
                        //加上自己的广告贴
                        $query = $query->where('replies.status','>', self::STATUS_DELETED );
                        if( $uid ){
                            $query = $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
                        }
                    })
                        ->orWhere(function($query){
                            $uid = _uid();
                            //加上自己的广告贴
                            $query = $query->where('asks.status','>', self::STATUS_DELETED );
                            if( $uid ){
                                $query = $query->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
                            }
                        });
                })
                ->where( $tcTable.'.category_id', $category_id )
                ->valid();

            if( $orderByThread ){
                $query = $query->orderBy( 'c_time', 'DESC' )
                    ->select( $tcTable.'.*' )
                    ->selectRaw( 'CASE WHEN asks.create_time IS NOT NULL THEN asks.create_time WHEN replies.create_time IS NOT NULL THEN replies.create_time END as c_time');
            }
            else{
                //其他地方调用只会进入query 然 $orderByThread = false 这里用不到,故此
                $query = $query->orderBy($tcTable.'.create_time', 'DESC')
                    ->selectRaw( $tcTable.'.*, '.$tcTable.'.create_time as c_time' );
            }
        }



        if(isset($result['Replies'])){
            $Replies = $result['Replies'];
            $Replies = $Replies->where(function($query){
                $uid = _uid();
                //加上自己的广告贴
                $query->where('replies.status','>', self::STATUS_DELETED );
                if( $uid ){
                    $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
                }
            })
            ->where( $tcTable.'.category_id', $category_id )
            ->valid();
            if( $orderByThread ){
                $Replies = $Replies->select( $tcTable.'.*' )
                    ->selectRaw( 'CASE WHEN replies.create_time IS NOT NULL THEN replies.create_time END as c_time');
            }
            else{
                $Replies = $Replies->select( $tcTable.'.*' )
                    ->selectRaw( $tcTable.'create_time as c_time');
            }
        }
        if(isset($result['Asks'])){
            $Asks = $result['Asks'];
            $Asks = $Asks->Where(function($query){
                $uid = _uid();
                //加上自己的广告贴
                $query = $query->where('asks.status','>', self::STATUS_DELETED );
                if( $uid ){
                    $query->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
                }
            })
            ->where( $tcTable.'.category_id', $category_id )
            ->valid();
            if( $orderByThread ){
                $Asks = $Asks->select( $tcTable.'.*' )
                    ->selectRaw( 'CASE WHEN asks.create_time IS NOT NULL THEN asks.create_time END as c_time');
            }
            else{
                $Asks = $Asks->select( $tcTable.'.*' )
                    ->selectRaw( $tcTable.'create_time as c_time');
            }
        }


        //搜索求助和作品
        if($result['union'] === true){
            $query = $Replies->union($Asks);
        }elseif(isset($result['Replies'])){
            $query = $Replies;
        }elseif(isset($result['Asks'])){
            $query = $Asks;
        }
        $query->orderBy( 'c_time', 'DESC' );
        //        DB::enableQueryLog();
//        $query = $query->get();
//        dd(DB::getQueryLog());
        return $query->forPage( $page, $size )
                    ->get();
    }

    public function get_asks_by_category( $category_id, $status, $page, $size, $thread_status = NULL ){
        $tcTable = $this->table;
        if( !is_array( $status ) ){
            $status = [$status];
        }
        if( !is_array( $thread_status ) && !is_null( $thread_status ) ){
            $thread_status = [$thread_status];
        }
        return $this->leftjoin('asks', function($join) use ( $tcTable ){
                        $join->on( $tcTable.'.target_id', '=', 'asks.id')
                            ->where($tcTable.'.target_type', '=', self::TYPE_ASK);
                    })
                    ->where(function($query) use ( $thread_status ){
                        if( $thread_status ){
                            $query = $query->whereIn('asks.status', $thread_status );
                            return;
                        }

                        $uid = _uid();
                        //加上自己的广告贴
                        $query = $query->where('asks.status','>', self::STATUS_DELETED );
                        if( $uid ){
                            $query = $query->orWhere([ 'asks.uid'=>$uid, 'asks.status'=> self::STATUS_BLOCKED ]);
                        }
                    })
                    ->where( $tcTable.'.category_id', $category_id )
                    ->whereIn( $tcTable.'.status', $status )
                    //跟后台管理系统的时间保持一致
                    ->orderBy( 'asks.create_time', 'DESC')
                    ->forPage( $page, $size )
                    ->select( $tcTable.'.*' )
                    ->get();
    }

    public function get_valid_replies_by_category( $category_id, $page, $size ){
        $tcTable = $this->table;
        return $this->leftjoin('replies', function($join) use ( $tcTable ){
                        $join->on( $tcTable.'.target_id', '=', 'replies.id')
                            ->where($tcTable.'.target_type', '=', self::TYPE_REPLY);
                    })
                    ->where(function($query){
                        $uid = _uid();
                        //加上自己的广告贴
                        $query = $query->where('replies.status','>', self::STATUS_DELETED );
                        if( $uid ){
                            $query = $query->orWhere([ 'replies.uid'=>$uid, 'replies.status'=> self::STATUS_BLOCKED ]);
                        }
                    })
                    ->where( $tcTable.'.category_id', $category_id )
                    ->valid()
                    //跟后台管理系统的时间保持一致
                    ->orderBy( 'replies.create_time', 'DESC')
                    ->forPage( $page, $size )
                    ->select( $tcTable.'.*' )
                    ->get();
    }

    public function get_checked_threads( $category_ids, $page , $size ,$searchArguments = []){
        $tcTable = $this->table;
        $result = $this->searchKeyword( $searchArguments);
//        if($query['union'] === true){
//            $Replies = $query['Replies'];
//            $Replies = $Replies->checked()->ThreadsWhere($category_ids,$tcTable);
//            $Asks = $query['Asks'];
//            $Asks = $Asks->checked()->ThreadsWhere($category_ids,$tcTable);
//            $query = $Replies->union($Asks);
//        }
//        else{
//            if(isset($query['Replies'])){
//                $query = $query['Replies'];
//            }elseif(isset($query['Asks'])){
//                $query = $query['Asks'];
//            }else{
//                $query = $query['query'];
//            }
//            $query = $query->checked()->ThreadsWhere($category_ids,$tcTable);
//        }
        if(isset($result['query'])){
            $result = $result['query'];
            $query = $result->checked()->ThreadsWhere($category_ids,$tcTable);
        }
        if(isset($result['Replies'])){
            $Replies = $result['Replies'];
            $Replies = $Replies->checked()->ThreadsWhere($category_ids,$tcTable);
        }
        if(isset($result['Asks'])){
            $Asks = $result['Asks'];
            $Asks = $Asks->checked()->ThreadsWhere($category_ids,$tcTable);
        }
        if(isset($result['union'])){
            $query = $Replies->union($Asks);
        }



        $query = $query->orderBy('create_time', 'DESC')
        ->forPage( $page ,$size)->get();
        return $query;

    }
        public function get_threads_by_category_id( $category_id, $page = 1, $size = 15 ){
            $query = $this->where( 'category_id', $category_id )
                ->orderBy('id', 'DESC');
            if( $page && $size ){
                return $query->forPage( $page, $size )
                    ->get();
            }
            else{
                return $query->count();
        }

    }

    public function delete_thread( $uid, $target_type, $target_id, $status, $reason, $category_id ){
        $cond = [
            'target_type' => $target_type,
            'target_id' => $target_id
        ];
        if( $category_id ){
            $cond['category_id'] = $category_id;
        }
        $data = [
            'delete_by' => $uid,
            'status' => $status,
            'reason' => $reason
        ];

        return $this->where( $cond )->update( $data );
    }

    public function thread_has_parent_category_of( $target_type , $target_id, $parent_category_id ){
        return $this->leftjoin('categories', 'categories.id', '=', 'thread_categories.category_id')
                    ->where( 'target_type', $target_type )
                    ->where( 'target_id', $target_id )
                    ->where( 'pid', $parent_category_id )
                    ->exists();
    }

    public function searchKeyword(  $arguments )
    {
        //如果搜索作品或者求助 ID  必须有category_type
        $category_type = (isset($arguments['category_type']) && !empty($arguments['category_type'])) ? $arguments['category_type'] : null;
        $tcTable = (isset($arguments['table']) && !empty($arguments['table'])) ? $arguments['table'] : $this->table;
        $id = (isset($arguments['id']) && $arguments['id']) ? $arguments['id'] : null;
        $uid = (isset($arguments['uid']) && $arguments['uid']) ? $arguments['uid'] : null;
        $nickName = (isset($arguments['nickname']) && $arguments['nickname']) ? $arguments['nickname'] : null;
        $desc = (isset($arguments['desc']) && $arguments['desc']) ? $arguments['desc'] : null;
        $start_time = (isset($arguments['start_time']) && $arguments['start_time']) ? $arguments['start_time'] : null;
        $end_time = (isset($arguments['end_time']) && $arguments['end_time']) ? $arguments['end_time'] : null;
        //get_valid_threads_by_category 需要用到 屏蔽用户 ,这个不适搜所条件
        $users = (isset($arguments['users']) && $arguments['users']) ? $arguments['users'] : null;
        $result['union'] = false;

        if( $category_type || $id || $desc || $start_time || $end_time || $uid || $nickName) {
            if (self::CATEGORY_TYPE_REPLIES == $category_type || null == $category_type) {
                //echo 'ACTIVIT_TYPE_REPLIES';
                $Replies = $this->SearchKeywordReplies($id, $desc, $start_time, $end_time, $uid, $category_type, $tcTable,$users)
                    ->SearchKeywordUser('replies', $nickName);
                    //->ThreadsWhere($category_ids,$tcTable);
            }
            if (self::CATEGORY_TYPE_ASKS == $category_type || null == $category_type) {
                //echo 'ACTIVIT_TYPE_ASKS';
                $Asks = $this->SearchKeywordAsks($id, $desc, $start_time, $end_time, $uid, $category_type ,$tcTable,$users)
                    ->SearchKeywordUser('asks', $nickName);
                    //->ThreadsWhere($category_ids,$tcTable);
            }

            if(self::CATEGORY_TYPE_REPLIES == $category_type){
                $result['Replies'] = $Replies;
            }
            else if(self::CATEGORY_TYPE_ASKS == $category_type){
                $result['Asks'] = $Asks;
            }
            else if(null === $category_type){
                //echo 'union';
                //$query = $Replies->union($Asks);
                $result['Replies'] = $Replies;
                $result['Asks'] = $Asks;
                $result['union'] = true;
            }
        }
        else{
            $result['query'] = $this;
        }
//        $query = $query->orderBy('create_time', 'DESC');
//        DB::enableQueryLog();
//        $query = $query->get();
//        dd(DB::getQueryLog());
        /*ToDO $category_type > 2 bug*/
//        $query = $query->forPage( $page ,$size)->get();
        return $result;
    }

    public function scopeThreadsWhere($query,$category_ids,$tcTable)
    {
        $query->where( function($query) use ($category_ids,$tcTable) {
            if( $category_ids ){
                if( !is_array( $category_ids ) ){
                    $category_ids = [ $category_ids ];
                }
                $query->whereIn( 'category_id', $category_ids );
            }
        });
        return $query;
    }
    public function scopeSearchKeywordUser($query ,$tcTable = null ,$nickName = null)
    {

        //Todo nickname bad question
        if($tcTable && $nickName) {
            $query->join('users', function ($join) use ($nickName, $tcTable) {
                $join->on($tcTable . '.uid', '=', 'users.uid')
                    ->where('users.nickname','=',$nickName);
            });
        }
        return $query;
    }
    public function scopeSearchKeywordAsks( $query , $asksId = null ,$desc = null,$start_time = null,$end_time = null,$uid = null, $category_type =null ,$tcTable = null ,$users = null)
    {
        if($category_type || $tcTable || $asksId || $desc || $start_time || $end_time || $uid) {
            $query->select('thread_categories.*')->join('asks', function ($join) use ($tcTable, $asksId, $desc, $start_time,$end_time,$uid,$users) {
                $join->on($tcTable . '.target_id', '=', 'asks.id');
                $join->where($tcTable.'.target_type', '=', 1);
                if (!empty($desc)) {
                    $join->where('asks.desc', 'like', "%{$desc}%");
                }
                if (!empty($asksId)) {
                    $join->where('asks.id', '=', $asksId);
                }
                if (!empty($uid)) {
                    $join->where('asks.uid', '=', $uid);
                }
                if (!empty($start_time)) {
                    $join->where('asks.create_time', '>=', $start_time);
                }
                if (!empty($end_time)) {
                    $join->where('asks.create_time', '<=', $end_time);
                }
                if(!empty($users)) {
                    $join->whereNotIn("asks.uid", $users);
                }
            });//求助

//            $query = DB::table($tcTable)->select($tcTable.'.*')
//                ->where($this->table.'.status', self::STATUS_CHECKED)
//                ->orderBy('create_time','desc')
//                ->join('asks',function($join) use ($tcTable, $asksId, $desc, $start_time,$end_time,$uid)
//                        {
//                            $join = $join->on($tcTable . '.target_id', '=', 'asks.id')
//                                  ->where($tcTable.'.target_type', '=', 1);
//                            if (!empty($desc)) {
//                                $join->where('asks.desc', 'like', "%{$desc}%");
//                            }
//                            if (!empty($asksId)) {
//                                $join->where('asks.id', '=', $asksId);
//                            }
//                            if (!empty($uid)) {
//                                $join->where('asks.uid', '=', $uid);
//                            }
//                            if (!empty($start_time)) {
//                                $join->where('asks.create_time', '>=', $start_time);
//                            }
//                            if (!empty($end_time)) {
//                                $join->where('asks.create_time', '<=', $end_time);
//                            }
//                        });//求助
            return $query;

        }

    }
    public function scopeSearchKeywordReplies( $query , $repliesId = null ,$desc = null,$start_time = null,$end_time = null,$uid = null, $category_type = null ,$tcTable = null,$users = null)
    {
        if($category_type || $tcTable || $repliesId || $desc || $start_time || $end_time || $uid) {
            $query->select('thread_categories.*')->join('replies', function ($join) use ($tcTable, $repliesId, $desc, $start_time,$end_time,$uid,$users) {
                $join->on($tcTable . '.target_id', '=', 'replies.id');
                $join->where($tcTable.'.target_type', '=', 2);
                if (!empty($desc)) {
                    $join->where('replies.desc', 'like', "%{$desc}%");
                }
                if (!empty($repliesId)) {
                    $join->where('replies.id', '=', $repliesId);
                }
                if (!empty($uid)) {
                    $join->where('replies.uid', '=', $uid);
                }
                if (!empty($start_time)) {
                    $join->where('replies.create_time', '>=', $start_time);
                }
                if (!empty($end_time)) {
                    $join->where('replies.create_time', '<=', $end_time);
                }
                if(!empty($users)) {
                    $join->whereNotIn("replies.uid", $users);
                }
            });//作品
        }
//        $query = DB::table($tcTable)->select($tcTable.'.*')
//            ->where($this->table.'.status', self::STATUS_CHECKED)
//            ->orderBy('create_time','desc')
//            ->join('replies',function($join) use ($tcTable, $repliesId, $desc, $start_time,$end_time,$uid)
//            {
//                $join->on($tcTable . '.target_id', '=', 'replies.id')
//                    ->where($tcTable.'.target_type', '=', 2);
//                if (!empty($desc)) {
//                    $join->where('replies.desc', 'like', "%{$desc}%");
//                }
//                if (!empty($asksId)) {
//                    $join->where('replies.id', '=', $asksId);
//                }
//                if (!empty($uid)) {
//                    $join->where('replies.uid', '=', $uid);
//                }
//                if (!empty($start_time)) {
//                    $join->where('replies.create_time', '>=', $start_time);
//                }
//                if (!empty($end_time)) {
//                    $join->where('replies.create_time', '<=', $end_time);
//                }
//            });//求助
        return $query;
    }
}
