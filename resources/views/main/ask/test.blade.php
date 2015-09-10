@section('content')
<head>
<link rel="stylesheet" href="/main/css/test.css" type="text/css" >
</head>
<div class="commend-detail-container">
    <div class="commend-area">
        <div class="commend-item">
            <div class="commend-item-header">
                <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg">
                <span class="commend-item-info">
                    <span class="commend-item-user">
                        123
                    </span>
                    <span class="commend-item-created">
                        123
                    </span>
                </span>
               <span class="dowload-actionbar">
                    <span class="download-original">下载作品</span>
                    <span class="download-icon bg-sprite"></span>
               </span>
            </div>                

            <div class="commend-item-picture">
                <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg">
            </div> 

            <div class="commend-item-actionbar">   
                <span class="bg-sprite icon-like-large commendItem-actionbar-like-icon"></span> 
                <span class="commendItem-actionbar-like-count">123</span> 
                <span class="bg-sprite icon-comment-large commendItem-actionbar-comment-icon"></span>
                <span class="commendItem-actionbar-comment-count">123</span>
                <span class="commendItem-actionbar-share-icons right">
                    <span class="commendItem-actionbar-share-weibo bg-sprite icon-weibo-large"></span>
                    <span class="commendItem-actionbar-share-wechat bg-sprite icon-wechat-large"></span>
                    <span class="commendItem-actionbar-share-moments bg-sprite icon-moments-large"></span>
                </span>
            </div>
            <div class="commend-frame">
                <textarea name="" id="" ></textarea>
                <span class="commend-btn right">评论</span>   
            </div>
            <!-- 热门评论 -->
            <div class="commend-hot-content">
                <span class="commend-hot-title">
                    <span class="hot-icon bg-sprite"></span>
                    <span class="hot-font">最热评论</span>
                </span>
                <div class="hot-content">
                    <span class="commend-user-information">
                        <span class="commend-head-portrait">
                            <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg" alt="头像">
                        </span>
                        <span class="commend-nickname">刘金平</span>
                    </span>
                    <span class="commend-item-content">
                        <span class="commend-content">内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容</span>
                        <span class="hot-item-actionbar">
                            <span class="commend-release-time">1小时前</span>
                            <span class="actionbar-reply-link right">
                                <span class="reply-btn">回复</span>
                                <span class="link-icon bg-sprite"></span>
                                <span class="actionbar-like-count">11</span>
                            </span>
                        </span>
                    </span>
                    <span class="commend-reply-content">
                        <span class="commend-reply-area">
                            <textarea name="" id="" cols="30" rows="10"></textarea>
                        </span>
                        <span class="command-item-reply-btn">
                            <span class="commend-reply-btn right">回复</span>
                        </span>
                    </span>
                </div>
            </div>
            <!-- 最新评论 -->
            <div class="commend-newest-content">
                <span class="commend-newest-title">
                    <span class="newest-icon bg-sprite"></span>
                    <span class="newest-font">最热评论</span>
                </span>
                <div class="newest-content">
                    <span class="commend-user-information">
                        <span class="commend-head-portrait">
                            <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg" alt="头像">
                        </span>
                        <span class="commend-nickname">刘金平</span>
                    </span>
                    <span class="commend-item-content">
                        <span class="commend-content">内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容内容</span>
                        <span class="newest-item-actionbar">
                            <span class="commend-release-time">1小时前</span>
                            <span class="actionbar-reply-link right">
                                <span class="reply-btn">回复</span>
                                <span class="link-icon bg-sprite"></span>
                                <span class="actionbar-like-count">11</span>
                            </span>
                        </span>
                    </span>
                    <span class="commend-reply-content">
                        <span class="commend-reply-area">
                            <textarea name="" id="" cols="30" rows="10"></textarea>
                        </span>
                       <span class="command-item-reply-btn">
                            <span class="commend-reply-btn right">回复</span>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- QRCODE -->
    <div class="commend-QRCode">
        <span class="picture-QRCode">
            <img src="/main/img/WachatQrcode.png" alt="">
        </span>
        <span class="load-iphone-btn">
            <span class="iphone-icon bg-sprite"></span>
        </span>
        <span class="load-android-btn">
            <span class="android-icon bg-sprite"></span>
        </span>
    </div>
</div>

@endsection
