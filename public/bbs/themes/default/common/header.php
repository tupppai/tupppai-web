<!-- header nav  -->
<link rel="stylesheet" type="text/css" media="screen and (max-width:640px)"  href="/css/commonh5.css">
<div class="header-container header-container-bbs ">
    <div class="header-back" style="height: 45px;background:;" >
        <span class="bbs-logo" style="position: absolute; left: 100px; top: 6px;">
            <img src="/img/toplogo.jpg"  alt="logo">
        </span>
        <div class="user-message" style=" line-height: 38px;top: 1px;">
            <div class="profile-view hide">
                <ul>
                    <li class="avatar">
                        <span class="user-avatar" style="margin-top: 0;">
                            <span class="title-bar-setting" >
                                <div id="setting_panel" class="">
                                    <a class="move-style" id="personage" href="">个人主页</a>
                                    <a class="move-style">账号设置</a>
                                    <a class="move-style" id="logout" href="#logout">退出登录</a>
                                </div>
                            </span>
                            <img class="bbs-pic" style="margin-top: 10px;" src=" " alt="">
                        </span>
                    </li>
                    <li class="remind-message" style="height: 32px;margin-left: 8px;">
                    <a href="/#message/comment">
                        <i class="message-remind-icon bg-sprite-new" style="background-position: -7px -263px;"></i>
                        <!-- <i class="remind-red-dot-icon bg-sprite"></i>    -->
                    </a>
                    </li>
                </ul>
            </div>
             <div class="login-view hide">
                <!-- <li class="weibo"><i class="bg-sprite icon-weibo"></i>微博快速登录</li> -->
                <a href="#login-popup" class="login-popup"><li class="login">登录</li></a>
                <a href="#register-popup" class="register-popup"><li class="register">注册</li></a>
            </div>
            <ul>
                 <li class="tupai"><a target="_blank" href="/res/app/statics/recommend/recommend.html">图派介绍</a></li>
            <li class="app-tupai">客户端<span class="download-picture"></span></li>
                
                
            </ul>
        </div>

            </div>
    </div>
</div>
        <div class="header header-bbs"> 
            <div class="title-bar">
        <!--       
                上一个版本的logi          
                <a href="#asks">
                     <div class="left">
                        <span class="title-bar-logo icon-logo bg-sprite"></span>
                    </div>
                </a> 
                -->
                <div class="menu-bar">
                    <div class="menu-bar-area">
                        <a class="menu-bar-item" href="/#index">首页</a>
                        <a class="menu-bar-item" href="/#trend">动态</a>
                        <a class="menu-bar-item" href="/#channel/1001">频道</a>
                        <a class="menu-bar-item active" href="/bbs" style="height: 57px;">讨论</a>
                    </div>
                    
                    <div class="menu-search">
            <!--             <input type="text" style="width: 217px;" id="keyword" placeHolder="搜索用户或内容" />
                        <i class="search-icon bg-sprite-new"></i> -->
                <!--                   <span class="search-content">
                            <span class="search-header">
                                <i class="triangle-icon bg-sprite-new"></i>
                                <div class="correlation">相关用户</div>
                                <i class="more-icon bg-sprite-new"></i>
                            </span>
                            <span class="search-user">
                                    <span class="search-item">
                                        <img src="http://7u2spr.com1.z0.glb.clouddn.com/20151029-1645015631dc8d48505.jpg?imageView2/2/w/480" alt="">
                                        <span class="search-name">刘金平</span>
                                    </span>
                                    <span class="search-item">
                                        <img src="http://7u2spr.com1.z0.glb.clouddn.com/20151029-1421385631baf2690d2.jpg?imageView2/2/w/480" alt="">
                                        <span class="search-name">咩咩</span>
                                    </span>
                            </span>
                            <span class="correlation-content">
                                <div class="correlation">相关内容</div>
                                <i class="more-icon bg-sprite-new"></i>
                                <span class="correlation-list">
                                    <span class="correlation-font">啊发卡洛斯快递</span>
                                    <span class="correlation-font">啊发卡洛斯快递</span>
                                    <span class="correlation-font">啊发卡洛斯快递</span>
                                    <span class="correlation-font">啊发卡洛斯快递</span>
                                    <span class="correlation-font">啊发卡洛斯快递</span>
                                <div class="look-content">
                                    查看全部搜索结果
                                    <i class="more-icon bg-sprite-new"></i>
                                </div>
                                </span>
                            </span>
                        </span> -->
                    </div>
                </div>
                <!-- 
                    上一版本的登录
                     <div class="right setting" id="headerView"></div>
                 -->
            </div>
            <div class="clear"></div>        
        </div>

<script>
    $.get('/user/status',function(ret){
        if( ret.ret == '1') {
            var src = ret.data.avatar;
            $('.user-avatar img').attr('src',src);
            $('.login-view').addClass('hide');
            $('.profile-view').removeClass('hide');
           
        }else{
            $('.login-view').removeClass('hide');
            $('.upload-btn').addClass('hide');
            $('.profile-view').addClass('hide');
        }

        var uid = ret.data.uid;
        $('#personage').attr('href','/#homepage/reply/' + uid);
        $('#message').attr('href','/#message/' + uid);
    })
    $('.search-icon').click(function(){
        var keyword = $('#keyword').val();
        if(keyword != undefined && keyword != '') {
            location.href = '/#search/all/'+ keyword;
        }
        else {
            location.href = '/#search/'+ type;
        }
    });
    $('#logout').click(function(){
     
        $.get('/user/logout',function(a,b){
       
            if( b=='success'){
                location.href = '/bbs/';
                
                location.reload();
            }
        })
    });

</script>


