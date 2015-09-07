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
                    <a data-remodal-target="Wechar-Qrcode-modal" href="#Wechar-Qrcode-modal"><span class="login-by-wechat">微信快速登录</span></a>
                    <a data-remodal-target="login-modal" href="#login-modal"><span class="login-btn">登录</span></a>
                    <a data-remodal-target="Register_modal" href="#data-remodal-id=Register_modal"><span class="register-btn">注册</span></a>
                    @endif
                </div>
            </div>
            <div class="clear"></div>        
        
            <!-- menu bar nav  -->
            <div class="menu-bar">
                <div class="menu-bar-area">
                    <a class="menu-bar-item active" href="/index/index">求P</a>
                    <a class="menu-bar-item" href="/index/hot">热门</a>
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
    
<!-- TODOPINGGE 根据样式补全login框  -->
    <div class="remodal login-dialog" data-remodal-id="login-modal" role="dialog">
        <div class="login-panel">
            <div class="login-header">
                <div class="login-title">
                    登录
                </div>
                <div class="login-line-between"></div>
                <a data-remodal-target="Register_modal" href="#data-remodal-id=Register_modal">
                    <div class="register-title">
                        注册
                    </div>
                </a>
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
            <a data-remodal-target="Wechar-Qrcode-modal" href="#Wechar-Qrcode-modal">
               <div class="login-wechat-btn" id="login_wechat_btn">
                  <span class="login-wechat-icon bg-sprite"></span>
                    微信登录
                </div>
            </a>
        </div>
    </div> 

    <div class="remodal register-dialog" data-remodal-id="Register_modal" role="dialog">
        <div class="register-panel">
            <div class="login-header">
                <div class="login-title">
                    登录
                </div>
                <div class="login-line-between"></div>
                <a data-remodal-target="Register_modal" href="#data-remodal-id=Register_modal">
                    <div class="register-title">
                        注册
                    </div>
                </a>
            </div>
            <div class="register-nickname">
                <input type="text" placeholder="昵称" >
            </div>
            <div class="option-sex">
                性别:
                <div class="option-boy">
                    <input type="radio" class="option-boy-input bg-sprite" name="sex" >
                    <span>男</span>
                </div>
                <div class="option-girl">
                    <input type="radio" class="option-girl-input bg-sprite" name="sex">
                    <span>女</span>
                </div>
            </div>
            <div class="register-phone">
                <input type="text" placeholder="手机" >
            </div>
            <div class="verification-code">
                <input type="text" placeholder="验证码">
                <span class="send-verification-code">发送验证码</span>
                <span class="again-verification-code">重新发送(60s)</span>
            </div>
            <div class="register-password">
                <input type="text" placeholder="密码">
            </div>
            <div class="agree-deal">
                <input name="checkbox" type="checkbox" value="checkbox" checked="checked" />
                <a href="#"><span>我已阅读并同意图派协议</span></a>
            </div>
            <div class="register-btn">
                注册
            </div>
        </div>
    </div>

    <!-- Wechat qr code     -->
    <div class="Wechat-Qrcode remodal" data-remodal-id="Wechar-Qrcode-modal" role="dialog">
        <img src="/main/img/WachatQrcode.png" alt="微信二维码">
    </div>


</body>
</html>
