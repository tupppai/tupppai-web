<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Request, Session, Config, App, DB;

use App\Services\User as sUser;
use App\Services\Category as sCategory;
use App\Services\Feedback as sFeedback;
use App\Services\Usermeta as sUsermeta;
use App\Facades\CloudCDN;

class ControllerBase extends Controller
{
    public $_uid = '';
    public $is_staff = false;
    public $is_admin = false;
    public $layout     = 'index';
    protected $request = null;
    protected $controller = null;
    protected $action     = null;
    protected $admins  = array(1);

    const _PREFIX_ = "\App\Models\\";

    /**
     * 超级管理员ID
     */
    const SUPER_USER = 1;

    /**
     * 管理员身份ID
     */
    const ROLE_ADMIN = 1;

	public function __construct(Request $request)
    {
        $this->_uid = session('uid');
        if(env('APP_DEBUG') && !$this->_uid) {
            $this->_uid = 1;
        }
        $this->user = session('user');
        $this->request      = $request;
        $this->controller   = controller();
        $this->action       = action();
        $this->initialize();
    }

    protected function initialize() {
        if(Request::ajax()) {
            $this->_of = 'json';
        }
    }

    private function heartbeat(){
        /*
        \Heartbeat::init(\Heartbeat::DB_LOGON_ADMIN);
        $last_ontime = \Heartbeat::init(\Heartbeat::DB_LOGON)->last_ontime($this->_uid);
        if (isset($last_ontime) && ($last_ontime <= 0 || $last_ontime < $_SERVER['REQUEST_TIME'] - SESSION_EXPIRE)) {
            //TODO:登录超时15分钟或者被踢下线
        }
        Heartbeat_helper::init(Heartbeat_helper::DB_LOGON_SUP)->hello($this->user->tsup_user_id, session_id());
         */
    }

    public function get_match_columns(){
        $columns= $this->post("columns");
        // get display columns
        $match_columns = array();
        foreach($columns as $key=>$val){
            $match_columns[] = $val['data'];
        }
        return $match_columns;
    }

    private function build_query($builder, $cond){
        // get where query
        foreach($cond as $key=>$row){
            if(!is_array($row)){
                $row = "".$row;
            }
            if(!isset($row) or $row == ""){
                continue ;
            }

            if(is_array($row)){
                if(!isset($row[0]) or $row[0] == ""){
                    continue ;
                }

                if(isset($row[2]) == 'OR'){
                    if(isset($row[1])){
                        switch ($row[1]) {
                        case "DISTINCT":
                            $builder = $builder->distinct($row[0]);
                            $builder = $builder->select($row[0]);
                            break;
                        case "LIKE":
                            $builder = $builder->orWhere($key, 'LIKE', '%'.$row[0].'%');
                            break;
                        case "IN":
                            $values = $row[0];
                            if( !is_array( $values ) ){
                                $values = explode(',',$values);
                            }
                            $builder = $builder->orWhereIn($key, $values);
                            break;
                        case "NOT IN":
                            $builder = $builder->orWhereNotIn($key, explode(',', $row[0]));
                            break;
                        case "NULL":
                            $builder = $builder->orWhereNull($key);
                            break;
                        case "NOT NULL":
                            $builder = $builder->orWhereNotNull($key);
                            break;
                        case "BETWEEN":
                            $builder = $builder->orWhereBetween($key, $row[0]);
                            break;
                        default:
                            if( !in_array($row[1], array('<','<=','!=','>=','>')) ){
                                $row[1] = '=';
                            }
                            $builder = $builder->orWhere($key, $row[1], $row[0]);
                            break;
                        }
                    }
                }
                else{
                    if(isset($row[1])){
                        switch ($row[1]) {
                        case "DISTINCT":
                            $builder = $builder->distinct($row[0]);
                            $builder = $builder->select($row[0]);
                            break;
                        case "LIKE":
                            $builder = $builder->where($key, 'LIKE', '%'.$row[0].'%');
                            break;
                        case "IN":
                            $values = $row[0];
                            if( !is_array( $values ) ){
                                $values = explode(',',$values);
                            }
                            $builder = $builder->whereIn($key, $values);
                            break;
                        case "NOT IN":
                            $builder = $builder->whereNotIn($key, explode(',', $row[0]));
                            break;
                        case "NULL":
                            $builder = $builder->whereNull($key);
                            break;
                        case "NOT NULL":
                            $builder = $builder->whereNotNull($key);
                            break;
                        case "BETWEEN":
                            $builder = $builder->whereBetween($key, $row[0]);
                            break;
                        default:
                            if( !in_array($row[1], array('<','<=','!=','>=','>')) ){
                                $row[1] = '=';
                            }
                            $builder = $builder->where($key, $row[1], $row[0]);
                            break;
                        }
                    }
                }

            }
            else {
                $builder = $builder->where($key, '=', $row);
            }
        }
        return $builder;
    }

    public function page($model, $cond = array(), $join = array(), $order = array(), $group = array() ){
        $start  = $this->post("start", "int", 1);
        $length = $this->rowLength * $this->post("length", "int", 10);

        $_GET['page'] = $start;

        // get basic class name
        $table      = get_class($model);
        $table_name = $model->getTable();

        // get builder for filter
        //$builder = $this->modelsManager->createBuilder();
        //$builder->from($table);
        $builder = $model;

        // basic columns
        $columns = array();


        // get join all table columns
        foreach($join as $key=>$row){
            $join_table = self::_PREFIX_.$key;
            $join_table = new $join_table;

            $join_table_name    = $join_table->getTable();

            $columns[] = $join_table_name.".*";
            if(is_array($row)){
                $builder = $builder->leftJoin($join_table_name, $table_name.".".$row[0], "=", $join_table_name.".".$row[1]);
            }
            else {
                $builder = $builder->leftJoin($join_table_name, $table_name.".".$row, "=", $join_table_name.".".$row);
            }
        }

        $columns[] = $table_name.".*";
        $builder = $builder->select($columns);
        $builder = $this->build_query($builder, $cond);

        // sort data by table
        if(isset($_REQUEST['sort']) && empty($order)){
            $sort    = explode(' ', $_REQUEST['sort']);
            $builder = $builder->orderBy($table_name.".".$sort[0], $sort[1]);
        }

        //case: "id DESC, statud ASC"
        if( is_string( $order ) ){
            $order = explode(',', $order);
        }

        if( is_array( $order ) ){
            $order = array_filter( $order );

            foreach( $order as $key => $o ){
                // default: ['id'=>'DESC']
                $orderCol = $key;
                $orderType = $o;
                if( is_int( $key ) ){  // case: ['id', 'id DESC']
                    $oo = explode( ' ', $o );
                    $orderCol = $oo[0];
                    $orderType = isset($oo[1])?$oo[1]:NULL;
                }
                $builder = $builder->orderBy( $orderCol, $orderType );
            }
        }

        if( is_string($group) ){
            $group = explode(',', $group);
        }
        $group = array_unique(array_filter($group));
        if( !empty($group) ){
            $group   = implode(',', $group);
            $builder = $builder -> groupBy( $group );
        }
        $data   = $builder->paginate($length);
        $total  = $data->total();

        // empty or final result
        if($start > $total){
            return array(
                'data' => array(),
                'recordsTotal' => $total,
                'recordsFiltered' => $total
            );
        }
        return array(
            'data'=>$data,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        );
    }

    protected $rowLength = 1;
    /**
     * 根据rowLength进行数据分割
     */
    public function output_grid($data = array(), $total = 0){
        $draw   = isset($_REQUEST["draw"])?$_REQUEST["draw"]: 1;

        $info = array(
            'ret'   => 1,
            'draw'  => $draw++,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            //'data'  => $page->items,
            'info'  => '',
            'debug' => intval(env('APP_DEBUG')),
        );

        $result = array();
        for($i = 0; $i < sizeof($data); $i += $this->rowLength){
            $tmp = array();

            for($j = 0; $j < $this->rowLength; $j ++) {
                if(isset($data[$i+$j]))
                    $tmp[$j] = $data[$i+$j];
                else
                    break;
            }
            $result[] = $tmp;
        }
        $info['data'] = $result;

        return json_encode($info);
    }

    public function output_table($data = array(), $info = ""){
        $draw   = isset($_REQUEST["draw"])?$_REQUEST["draw"]: 1;

        $info = array(
            'ret'   => 1,
            'draw'  => $draw++,
            //'recordsTotal' => $total,
            //'recordsFiltered' => $total,
            //'data'  => $page->items,
            'info'  => $info,
            'debug' => intval(env('APP_DEBUG')),
        );
        if(!empty($data['data']) && !is_array($data['data']))
            $data['data'] = $data['data']->toArray()['data'];

        return json_encode(array_merge($info, $data));
    }

    public function output_html($data = array(), $info = "") {
        $user       = session('user');
        $controller = $this->controller;
        $action     = $this->action;

        $__categories = [];
        $categories = sCategory::getCategories('channels', 'valid' );
        foreach( $categories as $category ){
            $__categories[] = sCategory::detail( $category );
        }

        //反馈小红点
        $__messages = [];
        //$last_read_fb_time = sUsermeta::get( $this->_uid, 'last_read_feedback_time', 0 );
        $last_read_fb_time = 0;
        $feedbacks = sFeedback::listUnreadFeedbacks( $last_read_fb_time );
        $unread_feedback_count = count($feedbacks);
        foreach( $feedbacks as $feedback ){
            $__messages[] = sFeedback::detail( $feedback );
        }

        if( $this->layout ){
            view()->share('theme_dir', '/theme/');

            $content = view("admin.$controller.$action", $data);

            $layout  = view('admin.index', array(
                'user'=>$user,
                'content'=>$content,
                '__categories' => $__categories,
                '__messages' => $__messages,
                '__unread_feedback_count' => $unread_feedback_count
            ));
        }
        else {
            $data['__categories'] = $__categories;
            $layout  = view("admin.$controller.$action", $data);
        }

        return $layout;
    }

    public function format_image($src, $arr = array()){
        $src = CloudCDN::file_url($src);

        if(!empty($arr)){
            $type = $arr['type'];
            $model_id = $arr['model_id'];
            return "<a target='_blank' href='#' class='preview_link'><img height='200' src='$src' /></a>";
        }
        else {
            return "<img height='200' src='$src' />";
        }
    }
}
