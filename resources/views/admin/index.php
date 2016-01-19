<?php
if($user['role_id'] != 4) {
    $is_staff = false;
    $class = "";
}
else {
    $is_staff = true;
    $class = "disable-sidebar";
}
$active     =  "";

$menus = array(
    //重做!!!
    // "数据统计" => array(
    //  "求助分析" => '/stat/analyze?type=asks',
    //  "作品分析" => '/stat/analyze?type=replies',
    //  '<hr />' => '#',
    //  "求助与帖子比例" => '/stat/stats?type=threads',
    //  "注册用户男女比例" => '/stat/stats?type=users',
    //  "App设备比例" => '/stat/stats?type=os'
    //),
    '内容审核' =>[
        '总审核库' =>'/verify/thread',
        '热门内容审核' => '/verify/hot?type=unreviewed',
        '频道内容审核' => '/verify/channels',
        '活动内容审核' => '/verify/activities',
        '批量加入分类' => '/verify/temp',
    ],
    '内容查看' => [
        '原图列表及管理' => [
            '/invitation/help',
            '/invitation/delhelp'
        ],
        '作品列表及管理' => [
            '/invitation/work',
            '/invitation/delwork'
        ],
        '用户评论管理' => [
            '/comment/index?status=all',
            '/comment/index?status=blocked',
            '/comment/index?status=deleted'
        ],
    ],
    '分类管理'=>[
        '频道管理' => '/category/index',
        '活动管理' => [
            '/activity/index',
            '/activity/works'
        ],
        '标签管理' => [
            '/tag/index',
            '/tag/users',
            '/tag/threads',
        ],
        '首页频道排序' => '/category/index?all=true'
    ],
    '频道内容管理'=>[],
    '用户管理' => [
        '用户总列表' => '/personal/index',
        '明星用户审核' => [
            '/recommendation/index?type=unreviewed&role=star',
            '/recommendation/index?type=pending&role=star',
            '/recommendation/index?type=normal&role=star'
        ],
        '黑名单用户审核' => [
            '/recommendation/index?type=unreviewed&role=blacklist',
            '/recommendation/index?type=pending&role=blacklist',
            '/recommendation/index?type=normal&role=blacklist'
        ],
        '推荐大神' => [
            '/master/rec_list',
            '/master/master_list'
        ],
        '用户举报池' => '/inform/index'
    ],
    '个人工作台' => [
        '评论库' => '/commentStock/index',
        '马甲库' => '/puppet/index',
        '求助内容上传' => [
            '/reviewAsk/wait',
            "/reviewAsk/pass",
            "/reviewAsk/fail",
            "/reviewAsk/release",
            "/reviewAsk/upload"
        ],
        '作品内容上传' => [
            '/reviewReply/wait',
            "/reviewReply/pass",
            "/reviewReply/fail",
            "/reviewReply/release",
            "/reviewReply/upload"
        ],
        '静态页面' => [
            '/html/index',
            '/html/add'
        ]
    ],
    '小秘书' => [
        '用户反馈' => '/feedback/index',
        '系统消息' => [
            '/sysmsg/msg_list',
            '/sysmsg/new_msg'
        ],
        '推荐App' => '/app/index',
        '推荐Banner' => '/banner/index'
    ],
    '系统' => [
        '系统帐号管理' => [
            "/waistcoat/parttime",
            "/waistcoat/help",
            "/waistcoat/work",
            "/waistcoat/staff",
            "/waistcoat/junior",
            "/score/index"
        ],
        '角色管理' => '/role/index',
        '兼职作品审核' => [
            '/check/wait',
            '/check/pass',
            '/check/reject',
            '/check/release',
            '/check/delete',
        ],
        '后台排班' => '/scheduling/index',
        '权限模块' => '/role/list_permissions',
        '系统配置' => '/config/index',
        //'用户统计' => '/stat/index',
        '短信日志' => '/sms/index',
        '用户日志' => '/log/index'
    ],
    '交易系统'=> [
        '商品管理' => '/product/index',
        '批量充值' => '/account/recharge',
        '用户交易流水' => '/account/transactions'
    ]
);

foreach( $__categories as $__category ){
    $menus['频道内容管理'][$__category['display_name']] = '/verify/channels?status=valid&category_id='.$__category['id'].'&channel=1';
}

$title = [];
$menu_ul = "";
if(isset($_SERVER['REDIRECT_URL'])) {
    $request_uri = [$_SERVER['REDIRECT_URL'], $_SERVER['REQUEST_URI']];
}
else {
    $request_uri = [$_SERVER['REQUEST_URI']];
}
foreach($menus as $menu => $sub_menu){
    $open = "";
    if( !is_array($sub_menu)){
        if(in_array($sub_menu,$request_uri )){
            $open = "active open";
        }
        $menu_ul .= '<li class="'.$open.'">';
        $menu_ul .= '<a href="'.$sub_menu.'">';
        $menu_ul .= '<span class="title">'.$menu.'</span>';
        $menu_ul .= '</a>';
        $menu_ul .= '</li>';
        continue;
    }
    foreach($sub_menu as $in_sub_menu){
        if( !is_array($in_sub_menu) ){
            $in_sub_menu = [$in_sub_menu];
        }
        if( array_intersect( $in_sub_menu, $request_uri) ){
            //$title[] = $menu; // 一级分类
            $open = "active open";
        }
    }
    //if(in_array($request_uri, $sub_menu)) {
    //    $open = "active open";
    //}
    $menu_ul .= '<li class="'.$open.'">';
    $menu_ul .= '<a href="javascript:;">';
    $menu_ul .= '<span class="title">'.$menu.'</span>';
    $menu_ul .= '<span class="arrow '.$open.'"></span>';
    $menu_ul .= '</a>';
    $menu_ul .= '<ul class="sub-menu">';
    foreach($sub_menu as $text => $url){
        if( !is_array($url) ){
            $url = [$url];
        }
        if( array_intersect($request_uri, $url)){
            $title[] = $text;
            $menu_ul .= '<li class="active"><a href="'.$url[0].'">'.$text.'</a></li>';
        }
        else{
            $menu_ul .= '<li><a href="'.$url[0].'">'.$text.'</a></li>';
        }
    }
    $menu_ul .= '</ul>';
    $menu_ul .= '</li>';
}

$active     =  "";

$tabs = array(
    "审核作品" => array(
        "/check/wait",
        "/check/pass",
        "/check/reject",
        "/check/release"
    ),
    "举报数"   => "/inform/index",
    "帖子列表" => array(
        "/invitation/work",
        "/invitation/help",
        "/invitation/delwork",
        "/invitation/delhelp"
    ),
    "评论列表" => "/comment/index",
    "用户反馈"  =>  "/feedback/index",
    "创建账号记录" => "/personal/created_user"
);

$tab_content = '';
foreach($tabs as $menu => $sub_menu){
    $open = "";
    if(is_array($sub_menu)) {
        $url = $sub_menu[0];
    }
    else{
        $url  = $sub_menu;
    }

    $tab_content .= "
    <li class='dropdown dropdown-user'>
        <a href='$url' class='dropdown-toggle'>
            <span>
                $menu
                <span data='$menu' class='hidden notifications badge badge-danger'>0</span>
            </span>
        </a>
    </li>
    ";
}

$title = array_reverse( $title );
$title = implode(',', $title );
$prefix = (env('APP_DEBUG'))?'[测]':'[正]';

?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="backend management" name="description"/>
<meta content="jq" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo $theme_dir; ?>assets/global/plugins/font-awesome/css/font-awesome.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $theme_dir; ?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap/css/bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $theme_dir; ?>assets/global/plugins/uniform/css/uniform.default.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<!-- DOC: To use 'rounded corners' style just load 'components-rounded.css' stylesheet instead of 'components.css' in the below style tag -->

<link href="<?php echo $theme_dir; ?>assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="<?php echo $theme_dir; ?>assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $theme_dir; ?>assets/global/plugins/pace/themes/pace-theme-barber-shop.css" rel="stylesheet" type="text/css"/>
<!-- paginate -->
<link href="<?php echo $theme_dir; ?>assets/global/plugins/jquery-paginate/jquery.paginate.css" rel="stylesheet" type="text/css"/>

<!-- END THEME STYLES -->
<!-- <link rel="shortcut icon" href="/favicon.ico"/> -->
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/jquery.lazyload.js" type="text/javascript"></script>
<script src="<?php echo $theme_dir; ?>vendors/uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
<title> <?php echo $prefix.$title.'-';?>大神专用</title>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-quick-sidebar-over-content page-style-square">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="index-2.html">
			 <img style="height:40px; margin-top: 2px;" src="/favicon.ico" alt="logo" class="logo-default"/>
             <span style="font-size: 26px;vertical-align: middle;color: #ccc;text-decoration: none;">图派</span>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <!-- BEGIN TASK BUTTON-->
                <?php if($is_staff) echo $tab_content; ?>
                <li>&nbsp;</li>
                <li>&nbsp;</li>
                <li>&nbsp;</li>
                <li>&nbsp;</li>
                <!-- END TASK BUTTON-->
				<!-- BEGIN NOTIFICATION DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-bell"></i>
					<span class="badge badge-default">0</span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3><span class="bold">0 pending</span> notifications</h3>
							<a href="extra_profile.html">view all</a>
						</li>
						<li>
							<ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
							</ul>
						</li>
					</ul>
				</li>
				<!-- END NOTIFICATION DROPDOWN -->
				<!-- BEGIN INBOX DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-envelope-open"></i>
					<span class="badge badge-default">
					0 </span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3>You have <span class="bold">0 New</span> Messages</h3>
							<a href="page_inbox.html">view all</a>
						</li>
						<li>
							<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
							</ul>
						</li>
					</ul>
				</li>
				<!-- END INBOX DROPDOWN -->
				<!-- BEGIN TODO DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-extended dropdown-tasks" id="header_task_bar">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-calendar"></i>
					<span class="badge badge-default">0</span>
					</a>
					<ul class="dropdown-menu extended tasks">
						<li class="external">
							<h3>You have <span class="bold">0 pending</span> tasks</h3>
							<a href="page_todo.html">view all</a>
						</li>
						<li>
							<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
							</ul>
						</li>
					</ul>
				</li>
				<!-- END TODO DROPDOWN -->
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<img alt="" class="img-circle" src="<?php echo $user['avatar']; ?>"/>
					<span class="username username-hide-on-mobile">
                    <?php echo $user['username'] ?>
					<span class="badge badge-danger">0</span>
                    </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
                            <a href="/score/index?operate_id=<?php echo $user['uid'];?>">
							<i class="icon-user"></i> 结算记录 </a>
						</li>
						<li>
							<a href="/scheduling/index?uid=<?php echo $user['uid'];?>">
							<i class="icon-calendar"></i> 时间安排 </a>
						</li>
						<li>
							<a href="/">
                            <i class="icon-envelope-open"></i> 系统消息
                            <span class="badge badge-danger"> 0 </span>
							</a>
						</li>
						<li class="divider">
						</li>
						<li>
							<a href="extra_lock.html">
							<i class="icon-lock"></i> Lock Screen </a>
						</li>
						<li>
							<a href="/Login/logout">
							<i class="icon-key"></i> Log Out </a>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
				<!-- BEGIN QUICK SIDEBAR TOGGLER -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-quick-sidebar-toggler">
					<a href="/Login/logout" class="dropdown-toggle">
					<i class="icon-logout"></i>
					</a>
				</li>
				<!-- END QUICK SIDEBAR TOGGLER -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <!-- 如果是非后台管理员账号，禁止显示左侧导航条 -->
    <?php
    if(!$is_staff):
    ?>
	<div class="page-sidebar-wrapper" >
		<div class="page-sidebar navbar-collapse collapse">
			<ul class="page-sidebar-menu  sub-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="0">
				<li class="sidebar-toggler-wrapper">
					<div class="sidebar-toggler">
					</div>
					<li>
				</li>
				<li class="sidebar-search-wrapper">
					<form class="sidebar-search " action="" method="POST">
						<a href="javascript:;" class="remove">
						<i class="icon-close"></i>
						</a>
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Search...">
							<span class="input-group-btn">
							<a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
							</span>
						</div>
					</form>
                </li>
                <!-- PHPMENU -->
                <?php
                    echo $menu_ul;
                ?>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
    </div>
    <?php endif;?>
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
        <div class="page-content <?php echo $class?>" >
            <?php echo $content; ?>
		</div>
	</div>
	<!-- END QUICK SIDEBAR -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2015 &copy; PSGod by jq.
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/respond.min.js"></script>
<script src="<?php echo $theme_dir; ?>assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-migrate.min.js"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/jquery.blockui.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/jquery.cokie.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/uniform/jquery.uniform.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/bootstrap-toastr/toastr.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/bootbox/bootbox.min.js"></script>

<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/scripts/datatable.js"></script>
<!-- underscore -->
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/underscore/underscore-min.js"></script>
<!-- endless -->
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/jquery.endless-scroll-1.3.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/scripts/endless.js"></script>
<!-- paginate -->
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/plugins/jquery-paginate/jquery.paginate.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/scripts/paginate.js"></script>
<!--
    <script src="<?php echo $theme_dir; ?>assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
-->
<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/global/scripts/metronic.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/admin/layout/scripts/layout.js"></script>
<script type="text/javascript" src="<?php echo $theme_dir; ?>assets/scripts/common.js"></script>
<script>
Metronic.init(); // init metronic core componets
Layout.init(); // init layout
Common.init();
</script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {
    /*
    $("img").lazyload({
        placeholder : "/theme/img/favicon.ico"
    });
     */
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for(var i = 0; i < strs.length; i ++) {
            var key = strs[i].split("=")[0];
            var val = strs[i].split("=")[1];

            $("input[name='"+key+"']").val(val);

        }
        table && table.submitFilter();
    }

    function beat(){
        $.ajax({
            url: '/user/beat',
            loading: false,
            success: function(data){
                var data = JSON.parse(data);
                var t = data.notifications;
                for( var i in t ){
                    $("span.notifications[data='"+i+"']").addClass('hidden');
                    if(t[i] > 0) {
                        $("span.notifications[data='"+i+"']").text(t[i]);
                        $("span.notifications[data='"+i+"']").removeClass('hidden');
                    }
                }
            }
        });
        setTimeout(beat, 20000);
    };
    <?php
    if($is_staff) {
        echo 'setTimeout(beat, 5000);';
    }
    ?>
   //Metronic.init(); // init metronic core componets
   //Layout.init(); // init layout
   /*
   QuickSidebar.init(); // init quick sidebar
   Demo.init(); // init demo features
   Index.init();
   Index.initDashboardDaterange();
   Index.initJQVMAP(); // init index page's custom scripts
   Index.initCalendar(); // init index page's custom scripts
   Index.initCharts(); // init index page's custom scripts
   Index.initChat();
   Index.initMiniCharts();
   Tasks.initDashboardWidget();
    */
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
<!-- Mirrored from www.keenthemes.com/preview/metronic/theme/templates/admin/ by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 17 Dec 2014 05:04:51 GMT -->
</html>
