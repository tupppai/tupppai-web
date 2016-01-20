<?php namespace App\Http\Controllers\Admin;
use Carbon\Carbon;

use App\Models\User as mUser;
use App\Models\Ask as mAsk;
use App\Models\Reply as mReply;
use App\Models\Download as mDownload;
use App\Models\Comment as mComment;
use App\Models\Follow as mFollow;
use App\Models\Count as mCount;

use App\Services\Count as sCount;

use DB;

class StatController extends ControllerBase{

    /*
    public function initialize(){
        parent::initialize();
        $this->assets->addJs('HighCharts/js/highcharts.js');
        $this->assets->addCss('theme/assets/global/css/stat.css');
        $this->assets->addJs('theme/assets/global/scripts/stat.js');
    }
    */

    public function indexAction() {

        $data = array();
        $data['user_count'] = mUser::count();

        $data['only_ask_user_count'] = mAsk::whereNotIn("asks.uid", function($query) {
            $query->from('replies')
                ->select('replies.uid')
                ->distinct();
            })
            ->distinct()
            ->select('uid')
            ->count('uid');

        $data['reply_user_count'] = mReply::distinct()
            ->select('uid')
            ->count('uid');

        $data['download_user_count'] = mDownload::distinct()
            ->select('uid')
            ->count('uid');

        $data['male_count'] = mUser::where('sex', 1)
            ->count();

        $data['female_count'] = mUser::where('sex', 0)
            ->count();

        $data['comment_count'] = mComment::count();

        $data['like_count']     = mCount::where('action', sCount::ACTION_UP)->count();
        $data['share_count']    = mCount::where('action', sCount::ACTION_SHARE)->count();
        $data['collect_count']  = mCount::where('action', sCount::ACTION_COLLECT)->count();
        $data['follow_focus_count']  = DB::select(DB::raw("select count(`follow`.`id`) as `count` from `follows` as `follow` inner join `follows` as `fan` on `follow`.`follow_who` = `fan`.`uid` where `follow`.`uid` = `fan`.`follow_who`"))[0]->count;

        $data['ask_count'] = mAsk::count();
        $data['reply_count'] = mReply::count();
        $data['download_count'] = mDownload::valid()->count();

        $data['ask_no_replies'] = DB::table('asks')->selectRaw('count(asks.id)')
                    ->leftJoin( 'replies', function( $join ) {
                        $join->on( 'replies.ask_id', '=', 'asks.id' );
                    })
                        ->whereNull('replies.id')
                        ->distinct()
                        ->select('asks.id')
                        ->count('asks.id');


        $data['ask_has_replies'] = DB::table('asks')->selectRaw('count(asks.id)')
                    ->leftJoin( 'replies', function( $join ) {
                        $join->on( 'replies.ask_id', '=', 'asks.id' );
                    })
                        ->whereNotNull('replies.id')
                        ->distinct()
                        ->select('asks.id')
                        ->count('asks.id');

        $data['ask_one_reply'] = sizeof(mReply::selectRaw('count(ask_id) as ask_count, ask_id')
            ->groupBy('ask_id')
            ->havingRaw('ask_count = 1')
            ->get());

        $date = $this->get('date', 'string', date("Ymd"));
        $time = strtotime($date);
        $tomo = $time + 24*60*60;
        $data['today_user_count'] = mUser::where('create_time', '>', $time)
            ->where('create_time', '<', $tomo)
            ->count();
        $data['today_ask_count'] = mAsk::where('create_time', '>', $time)
            ->where('create_time', '<', $tomo)
            ->count();
        $data['today_reply_count'] = mReply::where('create_time', '>', $time)
            ->where('create_time', '<', $tomo)
            ->count();
        $data['today_download_count'] = mDownload::where('create_time', '>', $time)
            ->where('create_time', '<', $tomo)
            ->count();

        return $this->output($data);
    }




    /*   ========= for sky ========== */
    private function phqlFetch( $phql ){
        $pdo = \Phalcon\DI::getDefault()->getDb();
        $sql = $pdo->prepare($phql);
        $sql ->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $sql->execute();
        $res = $sql->fetchAll();
        return $res;
    }

    public function statAction(){
        $target    = $this->get( 'target', 'string' );  //类型(用户，帖子，评论，设备)
        $category  = $this->get( 'category', 'string' );  //指标（性别，注册时间）

        $span  = $this->get( 'span', 'string', 'hoursxx' );  //时间跨度
        $startFrom = $this->get( 'startFrom', 'int', Carbon::now()->addDay()->timestamp ); //开始时间
        $endAt = $this->get( 'endAt', 'int', Carbon::now()->timestamp );  //结束时间
        $startFrom = Carbon::now()->timestamp( $startFrom );
        $endAt = Carbon::now()->timestamp( $endAt );

        $data = [];
        // $span
        switch ( $target ) {
            case 'user':
                $data = self::user( $category, $span, $startFrom, $endAt );
                break;

            default:
                # code...
                break;
        }


        return $this->output_json( [
            'result' => 'ok',
            'points' => $data,
            'startFrom' => $startFrom->toDateTimeString(),
            'endAt' => $endAt->toDateTimeString()
        ]);
    }

    public function user( $category, $span, $startFrom, $endAt ){
        $groupby = '';
        $slctCols = [];

        $query = new mUser();

        switch ( $category ) {
            case 'gender':
                $slctCols = array_merge( $slctCols, [ 'sex', 'count(sex) as c' ] );
                $query = $query->groupBy( 'sex' );
                break;
            default:
                # code...
                break;
        }

        switch( $span ){
            case 'hours':
                $slctCols = array_merge( $slctCols, ['MONTH(FROM_UNIXTIME(create_time)) as month'] );
                $query->groupBy('month');
                break;
            case 'days':
                break;
            case 'day':
                break;
            case 'date':
                break;
            case 'dates':
                break;
            default:
                $groupby = 'sex';
                break;
        }
        if( $groupby ){
            $query->groupBy($groupby);
        }

        $query = $query->selectRaw( implode(',', $slctCols ) );
        $query = $query->whereBetween('users.create_time', [ $startFrom->timestamp, $endAt->timestamp ] );
        $stat = $query->get();
// dd($stat);
        $data = [];
        switch ( $category ) {
            case 'gender':
                $data = [
                    //隐含条件，女的index为0，男的index为1
                    [
                        'name' => '女',
                        'y' => 0
                    ],[
                        'name' => '男',
                        'y' => 0
                    ],[
                        'name' => '未知',
                        'y' => 0
                    ]
                ]; //i18n
                $data_arr = $stat->toArray();
                foreach( $data_arr as $key => $val ){
                    if( !isset( $data[$val['sex']] ) ){
                        $val['sex'] = -1;
                    }
                    $data[ $val['sex'] ]['y'] = $val['c'];
                }
                break;

            default:
                # code...
                break;
        }

        return $data;
    }
}
