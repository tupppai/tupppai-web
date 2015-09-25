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
    <link rel="stylesheet" type="text/css" href="/main/css/common.css?121">
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
                </div>
        
            <!-- menu bar nav  -->
            <div class="menu-bar left">
                <div class="menu-bar-area">
                    <a id="menu-bar-ask" class="menu-bar-item active" href="/index/index?type=new">求P</a>
                    <a id="menu-bar-hot" class="menu-bar-item" href="/index/index?type=hot">热门</a>
                    <!--  
                    <a class="menu-bar-item">专栏</a>
                    <a class="menu-bar-item">排行榜</a> 
                    -->
                    <a class="menu-bar-item-last" href="/app/download">app下载</a>
                </div>
            </div>
                <div class="right setting">
                <a class="menu-bar-upload-btn"  href="/user/home">上传作品</a>
                    @if (isset($_uid))
                    <span class="user-avatar">
                        <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15205355b72d5512571.jpg">
                    </span>
        
                    <!-- <span class="title-bar-tip icon-tip bg-sprite"></span> -->
                    
                    <span class="title-bar-setting icon-setting bg-sprite">
                    <!--     <div id="setting_panel">
                            <a>账号设置</a>
                            <a>退出登录</a>
                        </div> -->
                    </span>
                    <span class="title-bar-end icon-end bg-sprite">
                    
                    </span>
                    <!--     
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
                    -->
                    @else 
                    <span class="bg-sprite title-bar-wehcat icon-wechat-pressed"></span>
                    <a data-remodal-target="Wechar-Qrcode-modal" href="#Wechar-Qrcode-modal"><span class="login-by-wechat">微信快速登录</span></a>
                    <a data-remodal-target="login-modal" href="#login-modal"><span class="login-btn">登录</span></a>
                    <a data-remodal-target="Register-modal" href="#Register-modal"><span class="register-btn">注册</span></a>
                    @endif
                </div>
            </div>
            <div class="clear"></div>        
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
                <a data-remodal-target="Register-modal" href="#data-remodal-id=Register-modal">
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

    <div class="remodal register-dialog" data-remodal-id="Register-modal" role="dialog">
        <div class="register-panel">
            <div class="login-header">
                <div class="login-title">
                    登录
                </div>
                <div class="login-line-between"></div>
                <a data-remodal-target="Register-modal" href="#data-remodal-id=Register-modal">
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

    <!-- 微信登录弹窗 -->
    <div class="Wechat-Qrcode remodal" data-remodal-id="Wechat-Qrcode-modal" role="dialog">
        <img src="/main/img/WachatQrcode.png" alt="微信二维码">
    </div>

    <!-- 上传作品弹窗  -->
    <!-- uploading-production -->
    <div class="upload-work-modal remodal" data-remodal-id="uploading-modal" role="dialog">
        <span class="close-icon bg-sprite"></span>
        <span class="upload-header">上传作品</span>
        <div class="upload-main">
            <span class="upload-case">
                <div class="upload-middle">
                    <span class="upload-icon bg-sprite"></span>
                </div>
                <span class="upload-remind">上传图片</span>
            </span>
        </div>
        <div class="upload-accomplish right">
            完成234
        </div>
    </div>

    <!-- 查看图片详情弹窗 -->
    <div class="picture-popup remodal" data-remodal-id="picture-popup-modal" role="dialog">
        <span class="close-icon bg-sprite"></span>
        <div class="picture-product">
            <span class="popup-header">
                <span class="download-icon bg-sprite"></span>
                <span class="picture-download">下载作品</span>
            </span>
            <span class="picture-show">
                <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg" alt="">
            </span>
        </div>
        <div class="picture-original">
            <span class="popup-header">
                <span class="download-gray-icon bg-sprite"></span>
                <span class="picture-download">下载作品</span>
            </span>
            <span class="picture-show">
                <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150906-09180655eb944e9f4bb.jpg?imageView2/2/w/99999999" alt="">
            </span>
        </div>
    </div>

</body>
</html>
