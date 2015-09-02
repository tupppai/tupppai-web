@section('content')
<head>
<link rel="stylesheet" href="/main/css/show.css" type="text/css" >
</head>

<!-- TODOPINGGE 11111:  根据设计稿完善作品详情页样式  -->
<div class="ask-show-container">
    <!-- ask show area  -->
    <div class="ask-show-area">
        <!-- display reply items -->
        @foreach ($reply_items as $reply_item)
        <div class="reply-item">
            <div class="reply-item-header">
                <img src="{{ $reply_item['avatar'] }}">
                <span class="reply-item-info">
                    <span class="reply-item-user">
                        {{ $reply_item['nickname'] }}
                    </span>
                    <span class="reply-item-created">
                        {{ $reply_item['create_time'] }}
                    </span>
                </span>
                <span class="download-icon">下载作品<span>
            </div>                

            <div class="reply-item-content">
                <img src="{{ $reply_item['image_url'] }}">
            </div> 

            <div class="reply-item-actionbar">    
                <span class="bg-sprite icon-like-large replyItem-actionbar-like-icon"></span> 
                <span class="replyItem-actionbar-like-count">123</span> 
                <span class="bg-sprite icon-comment-large replyItem-actionbar-comment-icon"></span>
                <span class="replyItem-actionbar-comment-count">123</span>
                <span class="replyItem-actionbar-share-icons">
                    <span class="replyItem-actionbar-share-weibo bg-sprite icon-weibo-large"></span>
                    <span class="replyItem-actionbar-share-moments bg-sprite icon-moments-large"></span>
                    <span class="replyItem-actionbar-share-wechat bg-sprite icon-wechat-large"></span>
                </span>
            </div>
        </div>
        @endforeach
    </div>    

    <!-- arrow area  -->
    <div class="ask-show-arrow"></div>

    <!-- origin picture area  -->
    <div class="ask-origin-pic">
        <div class="ask-origin-header">
            <img src="{{ $ask_item['avatar'] }}">
            <span class="ask-origin-info">
                <span class="ask-origin-user">{{ $ask_item['nickname']  }}</span>
                <span class="ask-origin-created">{{ $ask_item['create_time']  }}</span>
            </span>
            <span class="download-icon">下载原图</span>
        </div> 
        <div class="ask-origin-content">
            <img src="{{ $ask_item['image_url'] }}">
        </div>

        <div class="ask-origin-actionbar"></div>
    </div>
</div>

@endsection
