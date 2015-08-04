<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Request, Session, Config, App;

use App\Services\User as sUser;

class ControllerBase extends Controller
{
    public $_uid = '';
    public $is_staff = false;
    public $is_admin = false;
    private $admins = array(1);

    const _PREFIX_ = "App\Models\\";

    /**
     * 超级管理员ID
     */
    const SUPER_USER = 1;

    /**
     * 管理员身份ID
     */
    const ROLE_ADMIN = 1;

	public function __construct()
    {
        $this->_uid = session('uid');
        $this->user = session('user');
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

    private function get_where_query($cond){
        // get where query
        $query = "1 ";
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
                $oper = " AND ";
                if(isset($row[2])){
                    $oper = " ".$row[2]." ";
                }
                $query .= "$oper $key ";
                if(isset($row[1])){
                    switch ($row[1]) {
                        case "LIKE":
                            $query .= " LIKE '%".$row[0]."%'";
                            break;
                        case "IN":
                            $query .= " in (".$row[0].")";
                            break;
                        case "!=":
                            $query .= '!=\''.$row[0].'\'';
                            break;
                        case "NULL":
                            $query .= ' IS NULL';
                            break;
                        case "NOT NULL":
                            $query .= ' IS NOT NULL';
                            break;
                        default:
                            if( !in_array($row[1], array('<','<=','!=','>=','>')) ){
                                $row[1] = '=';
                            }
                            $query .= "$row[1]'$row[0]'";
                            break;
                    }
                }
            }
            else {
                $query .= " AND $key = '$row'";
            }
        }
        return $query;
    }

    public function page($model, $cond = array(), $join = array(), $order = array(), $group = array() ){
        $start  = $this->post("start", "int", 1);
        $length = $this->post("length", "int", 10);

        // get basic class name
        $table  = get_class($model);

        // get builder for filter
        $builder = $this->modelsManager->createBuilder();
        $builder->from($table);

        // basic columns
        $columns = array();



        // get join all table columns
        foreach($join as $key=>$row){
            $join_table = self::_PREFIX_.$key;
            $columns[] = $join_table.".*";
            if(is_array($row)){
                $builder->leftjoin($join_table, "$table.$row[0] = $join_table.$row[1]");
            }
            else {
                $builder->leftjoin($join_table, "$table.$row = $join_table.$row");
            }
        }

        $columns[] = $table.".*";
        $builder->columns($columns);

        $query = $this->get_where_query($cond);

        // sort data by table
        if(isset($_REQUEST['sort']) && !isset($cond['order'])){
            $builder->orderBy($table.".".$_REQUEST['sort']);
        }

        if( is_array( $order ) && !empty($order)){
            $order = implode(',',$order);
            $builder->orderBy($order);
        }
        $builder->where($query);

        if( is_string($group) ){
            $group = explode(',', $group);
        }
        $group = array_unique(array_filter($group));
        if( !empty($group) ){
            $group = implode(',', $group);
            $builder -> groupBy( $group );
        }

        //pr($builder->getPhql());
        $data = $builder->getQuery()->execute();
        $total = $data->count();

        // empty or final result
        if($start > $total){
            return array(
                'data' => array(),
                'recordsTotal' => $total,
                'recordsFiltered' => $total
            );
        }

        // page
        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $data,
                "limit"=> $length,
                "page" => intval($start/$length) + 1
            )
        );
        $page = $paginator->getPaginate();

        $items = $page->items;

        if(isset($items) && sizeof($items) > 0 && sizeof($items['0']) > 1){
            $items = array();
            foreach($page->items as $key=>$row){
                $item = new \stdClass;
                foreach($row as $key=>$val){
                    foreach($val as $in_key=>$data){
                        $item->$in_key = $data;
                    }
                }
                $items[] = $item;
            }
        }

        return array(
            'data'=>$items,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        );
    }

    public function output_table($data = array(), $info = ""){
        $this->noview();
        $draw   = isset($_REQUEST["draw"])?$_REQUEST["draw"]: 1;

        $info = array(
            'ret'   => 1,
            'draw'  => $draw++,
            //'recordsTotal' => $total,
            //'recordsFiltered' => $total,
            //'data'  => $page->items,
            'info'  => $info,
            'debug' => intval(DEV),
        );

        echo json_encode(array_merge($info, $data));
    }

    public function format_image($src, $arr = array()){
        $src = get_cloudcdn_url($src);

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
