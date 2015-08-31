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
                    @if (!isset($_uid))
                    <span class="user-avatar">
                        <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15205355b72d5512571.jpg">
                    </span>
        
                    <span class="title-bar-setting icon-setting bg-sprite">
                        <div id="setting_panel">
                            <a>s</a>
                            <a>s</a>
                        </div>
                    </span>
                    
                    <span class="title-bar-tip icon-tip bg-sprite"></span>
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
                    <span class="login-btn">登录</span>
                    <span class="register-btn">注册</span>
                    @endif
                </div>
            </div>
            <div class="clear"></div>        
        
            <!-- menu bar nav  -->
            <div class="menu-bar">
                <div class="menu-bar-area">
                    <span class="menu-bar-item active">求P</span>
                    <span class="menu-bar-item">热门</span>
                    <span class="menu-bar-item">专栏</span>
                    <span class="menu-bar-item">排行榜</span>
                    <span class="menu-bar-item-last">app下载</span>
                    <span class="menu-bar-upload-btn">上传作品</span>
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

    <div class="remodal" data-remodal-id="login-modal" role="dialog">
        //TODOPINGGE 根据设计稿 完成登录样式
        test
    </div>     
</body>
</html>
