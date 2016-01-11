@section('content')
<head>
<link rel="stylesheet" href="/main/css/ask-detail.css" type="text/css" >
</head>
<!-- TODO: 这个应该不能叫comment-item吧，这个是ask范围 -->
<div class="asks-detail-container">
    <div class="asks-area">
        <div class="asks-item">
            <div class="asks-item-header">
                <img src="{{ $ask_item['avatar']  }}" alt="{{ $ask_item['nickname']  }}">
                <span class="asks-item-info">
                    <span class="asks-item-user">
                        {{ $ask_item['nickname'] }}
                    </span>
                    <span class="asks-item-created">
                        {{ $ask_item['create_time'] }}
                    </span>
                </span>
               <span class="dowload-actionbar">
                    <span class="download-original">下载原图</span>
                    <span class="download-icon bg-sprite"></span>
               </span>
            </div>                

            <div class="asks-item-picture">
                <img src="{{ $ask_item['image_url'] }}">
            </div> 

            <div class="asks-item-actionbar">   
                <span class="bg-sprite icon-like-large"></span> 
                <span class="asksItem-actionbar-like-count">{{ $ask_item['up_count'] }}</span> 
                <span class="bg-sprite icon-comment-large asksItem-actionbar-comment-icon"></span>
                <span class="asksItem-actionbar-comment-count">{{ $ask_item['comment_count'] }}</span>
                <span class="asksItem-actionbar-share-icons right">
                    <span class="asksItem-actionbar-share-weibo bg-sprite icon-weibo-large"></span>
                    <span class="asksItem-actionbar-share-wechat bg-sprite icon-wechat-large"></span>
                    <span class="asksItem-actionbar-share-moments bg-sprite icon-moments-large"></span>
                </span>
            </div>
            <div class="commend-frame">
                <textarea name="" id="" ></textarea>
                <span class="commend-btn right">评论</span>   
            </div>
            <div class="clear"></div>
            <!-- 热门评论 -->
            @if (sizeof($ask_item['comments']['hot_comments']) != 0)
            <div class="commend-hot-content">
                <span class="commend-hot-title">
                    <span class="hot-icon bg-sprite"></span>
                    <span class="hot-font">最热评论</span>
                </span>
                @foreach ($ask_item['comments']['hot_comments'] as $comment)
                <div class="hot-content ">
                    <span class="commend-user-information">
                        <span class="commend-head-portrait">
                            <img src="{{ $comment['avatar']  }}" alt="{{ $comment['nickname']  }}">
                        </span>
                        <span class="commend-nickname">{{ $comment['nickname'] }}</span>
                    </span>
                    <span class="commend-item-content">
                        <span class="commend-content">{{ $comment['content'] }}</span>
                        <span class="hot-item-actionbar">
                            <span class="commend-release-time">{{ $comment['create_time'] }}</span>
                            <span class="actionbar-reply-link right">
                                <span class="reply-btn">回复</span>
                                <span class="link-icon bg-sprite"></span>
                                <span class="actionbar-like-count">{{ $comment['up_count'] }}</span>
                            </span>
                        </span>
                    </span>
                    <span class="commend-reply-content hide">
                        <span class="commend-reply-area">
                            <textarea name="" id="" cols="30" rows="10"></textarea>
                        </span>
                        <span class="command-item-reply-btn">
                            <span class="commend-reply-btn right">回复</span>
                        </span>
                    </span>
                </div>
                @endforeach
            </div>
            @endif
            <!-- 最新评论 -->
            @if (sizeof($ask_item['comments']['new_comments']) != 0)
            <div class="commend-newest-content">
                <span class="commend-newest-title">
                    <span class="newest-icon bg-sprite"></span>
                    <span class="newest-font">最新评论</span>
                </span>
                @foreach ($ask_item['comments']['new_comments'] as $comment)
                <div class="newest-content">
                    <span class="commend-user-information">
                        <span class="commend-head-portrait">
                            <img src="{{ $comment['avatar']  }}" alt="{{ $comment['nickname']  }}">
                        </span>
                        <span class="commend-nickname">{{ $comment['nickname'] }}</span>
                    </span>
                    <span class="commend-item-content">
                        <span class="commend-content">你好
                           <span class="">//<i class="reply-symbol">@</i>刘金平:大家好</span>
                        </span>
                        <span class="newest-item-actionbar">
                            <span class="commend-release-time">{{ $comment['create_time'] }}</span>
                            <span class="actionbar-reply-link right">
                                <span class="reply-btn">回复</span>
                                <span class="link-icon bg-sprite"></span>
                                <span class="actionbar-like-count">{{ $comment['up_count'] }}</span>
                            </span>
                        </span>
                    </span>
                    <span class="commend-reply-content commend-area-hide">
                        <span class="commend-reply-area">
                            <textarea name="" id="" cols="30" rows="10"></textarea>
                        </span>
                       <span class="command-item-reply-btn">
                            <span class="commend-reply-btn right">回复</span>
                        </span>
                    </span>
                </div>
                @endforeach
            </div>
            @endif
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
