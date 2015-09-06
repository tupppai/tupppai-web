<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="求PS大神">
    <meta name="keywords" content="PS,社区">

    <link rel="stylesheet" type="text/css" href="/main/css/libs/remodal.css" >
    <link rel="stylesheet" type="text/css" href="/main/css/libs/remodal-default-theme.css" >
    <link rel="stylesheet" type="text/css" href="/main/css/common.css">
    <link rel="stylesheet" type="text/css" href="/main/css/index.css">

    <script type="text/javascript" src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>    
    <script type="text/javascript" src="/main/vendor/node_modules/underscore/underscore-min.js"></script>
    <script type="text/javascript" src="/main/vendor/node_modules/backbone/backbone-min.js"></script>
    <script type="text/javascript" src="/main/js/libs/remodal.js"></script>
    <script type="text/javascript" src="/main/js/common.js"></script>
</head> 

<body>
    <div class="container">
        <!-- header nav  -->
        <div class="header">
            <!-- title nav -->
            <div class="title-bar">
                <div class="left">
                    <span class="title-bar-logo icon-logo bg-sprite">
                    </span>
                    <span class="title">求PS大神</span>
                </div>

                <div class="right">
                    @if (isset($_uid))
                    <span class="user-avatar">
                        <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15205355b72d5512571.jpg">
                    </span>
        
                    <span class="title-bar-tip icon-tip bg-sprite"></span>
                    
                    <span class="title-bar-setting icon-setting bg-sprite">
                        <div id="setting_panel">
                            <a>账号设置</a>
                            <a>退出登录</a>
                        </div>
                    </span>
                    
                    <span class="title-bar-rank">
                        <span class="title-rank-item">
                            大神排名:
                            <span class="title-rank-num">暂无</span>
                        </span>
                        <span class="title-rank-item">
                            专栏排名:
                            <span class="title-rank-num">1024</span>
                        </span>    
                    </span>
                    @else 
                    <span class="bg-sprite title-bar-wehcat icon-wechat-pressed"></span>
                    <span class="login-by-wechat">微信快速登录</span>
                    <span data-remodal-target="login-modal" class="login-btn">登录</span>
                    <span class="register-btn">注册</span>
                    @endif
                </div>
            </div>
            <div class="clear"></div>        
        
            <!-- menu bar nav  -->
            <div class="menu-bar">
                <div class="menu-bar-area">
                    <a class="menu-bar-item active" href="/index/index?type=new">求P</a>
                    <a class="menu-bar-item" href="/index/index?type=hot">热门</a>
                    <a class="menu-bar-item">专栏</a>
                    <a class="menu-bar-item">排行榜</a> 
                    <a class="menu-bar-item-last">app下载</a>
                    <a class="menu-bar-upload-btn">上传作品</a>
                </div>
            </div>
        </div>
        <div class="inner-container">
            @yield('content')
        </div>  

        <!-- footer -->
        <div class="footer">
            <span>©2015求PS大神</span>
        </div>  
    </div>
    
    <div class="remodal login-dialog" data-remodal-id="login-modal" role="dialog">
        <div class="login-panel">
            <div class="login-header">
                <div class="login-title">
                    登录
                </div>
                <div class="login-line-between"></div>
                <div class="register-title ">
                    注册
                </div>
            </div>
            <div class="login-name">
                <input id="login_name" type="text" name="loginName" placeholder="账号">
            </div>
            <div class="login-password bg-color">
                <input id="login_password" type="text" name="loginPassword" placeholder="密码">
            </div>
            <div class="forget-password">
                忘记密码？
            </div>
            <div class="login-btn" id="login_btn">登录</div>
            <div class="line-or">或</div>
            <a data-remodal-target="Wechat_Qrcode" href="#Wechat_Qrcode">
               <div class="login-wechat-btn" id="login_wechat_btn">
                  <span class="login-wechat-icon bg-sprite"></span>
                    微信登录
                </div>
            </a>
        </div>
    </div> 

    <!-- Wechat qr code-->
    <div class="Wechat-Qrcode remodal" data-remodal-id="Wechat_Qrcode" role="dialog">
        <img src="/main/img/WachatQrcode.png" alt="微信二维码">
    </div>


</body>
</html>
