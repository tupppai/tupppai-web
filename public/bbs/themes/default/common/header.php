<!-- header nav  -->
<div class="header-container">
    <div class="header-back" style="height: 45px;">
        <span class="bbs-logo" style="display: inline-block;
    width: 40px;
    height: 40px;
    top: 2px;
    position: absolute;
    left: 7px;">
            <img src="http://7u2spr.com1.z0.glb.clouddn.com/20151028-0115065630219abd8f1.jpg" style="width: 100%;" alt="">
        </span>
        <div class="user-message" style=" line-height: 38px;">
            <div class="profile-view hide">
                <ul>
                    <li class="avatar">
                        <span class="user-avatar" style="margin-top: 0;">
                            <span class="title-bar-setting">
                                <div id="setting_panel" class="">
                                    <a class="move-style" id="personage" href="">个人主页</a>
                                    <a class="move-style">账号设置</a>
                                    <a class="move-style" id="logout" href="#logout">退出登录</a>
                                </div>
                            </span>
                            <img src=" " alt="">
                        </span>
                    </li>
                    <li class="remind-message" style="height: 32px;margin-left: 3px;">
                    <a id="message" href="">
                        <i class="message-remind-icon bg-sprite"></i>
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
                <!-- <li class="tupai">关于图派</li> -->
               <a target="_blank" href="/download.html"> <li class="contact-us">联系我们</li></a>
            <li class="app-tupai">客户端<span class="download-picture"></span></li>
                
                
            </ul>
        </div>

            </div>
    </div>
</div>
        <div class="header"> 
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
                        <a class="menu-bar-item" href="/#dynamics">动态</a>
                        <a class="menu-bar-item" href="/#askflows">原图</a>
                        <a class="menu-bar-item" href="/#hotflows">作品</a>
                        <a class="menu-bar-item active" href="/bbs" style="height: 57px;">讨论</a>
                    </div>
                    
                    <div class="menu-search">
                        <input type="text" style="width: 217px;" id="keyword" placeHolder="搜索用户或内容" />
                        <i class="search-icon bg-sprite-new"></i>
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
        $('#personage').attr('href','/home.html/#home/ask/' + uid);
        $('#message').attr('href','/#message/' + uid);
    })
    
    $('#logout').click(function(){
     
        $.get('/user/logout',function(a,b ){
       
            if( b=='success'){
                location.href = '#asks';
                location.reload();
            }
        })
    });
</script>




<?php /*
<div id="navbar-wrapper">
<div  id="navigation" class="navbar <?php if($this->config->item('static')=='default'){?>navbar-inverse<?php } else{?>navbar-default<?php }?> navbar-fixed-top">
<div class="container">

	<div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
		<a class="navbar-brand" href="<?php echo site_url()?>"><?php echo $settings['logo'];?></a>
<!--<a class=".btn .btn-default navbar-btn collapsed" data-target=".navbar-collapse" data-toggle="collapse"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></a><a href="<?php echo site_url()?>" class="brand">Start<span class="green">BBS</span></a>-->
	</div>

        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li<?php if(@$action=='home'){?> class="active"<?php }?>><a href="<?php echo site_url()?>"><?php echo lang('front_home');?></a></li>
            <li<?php if(@$action=='node'){?> class="active"<?php }?>><a href="<?php echo site_url('node')?>">节点</a></li>
            <li<?php if(@$action=='user'){?> class="active"<?php }?>><a href="<?php echo site_url('user')?>">会员</a></li>
            <li<?php if(@$action=='tag'){?> class="active"<?php }?>><a href="<?php echo site_url('tag')?>">标签</a></li>
            <li<?php if(@$action=='add'){?> class="active"<?php }?>><a href="<?php echo site_url('topic/add')?>">发表</a></li>
           </ul>

        <?php echo form_open('search',array('class'=>'navbar-form navbar-left','target'=>'_blank','role'=>'search'))?>
		      <div class="form-group">
		        <input type="text" class="form-control" name="keyword" placeholder="输入关键字回车">
		      </div>
		</form>
          <ul class="nav navbar-nav navbar-right">
 
	        <?php if($this->session->userdata('uid')){ ?>
	        <li><a href="<?php echo site_url('message/')?>"><span class="glyphicon glyphicon-envelope"></span> <?php if($myinfo['messages_unread']>0) echo $myinfo['messages_unread']?></a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class='glyphicon glyphicon-user'></span> <?php echo $this->session->userdata('username');?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo site_url('user/profile/'.$this->session->userdata('uid').'')?>">个人主页</a></li>
                <li><a href="<?php echo site_url('message')?>">站内信</a></li>
                <li><a href="<?php echo site_url('settings')?>">设置</a></li>
                <?php if($this->auth->is_admin()){ ?>
                <li><a href="<?php echo site_url('admin/login')?>">管理后台</a></li>
                <?php }?>
                <li class="divider"></li>
                <!--<li class="dropdown-header">Nav header</li>-->
                <li><a href="<?php echo site_url('user/logout')?>">退出</a></li>
              </ul>
            </li>
			<?php }else{?>
            <li><a href="<?php echo site_url('user/register')?>">注册</a></li>
            <li><a href="<?php echo site_url('user/login')?>">登入</a></li>
            <?php }?>
          </ul>
        </div><!--/.nav-collapse -->
        
</div>
</div>

</div>

 */ ?>
