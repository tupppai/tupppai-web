<?php namespace App\Http\Controllers\Admin;
use Carbon\Carbon;

use App\Models\User as mUser;

class StatController extends ControllerBase{
    public function indexAction(){

        return $this->output();
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
