@section('content')
<!-- first item for advertisement  -->
<div class="photo-container">
    <div class="hot-first-item photo-item left QRcode-bgColor">
        <span class="QRCode">
            <span class="appDownload">免费上传求P图</span>            
        </span>
    </div> 
</div>

<!-- clear float -->
<div class="clear"></div>

<!-- Ask Item template  -->
<script type="text/template" id="ask-item-template">
<div class="photo-item left">
    <div class="photo-item-header hot-item-header">
        <a target="_blank" href="/user/home/<%= uid %>">
            <img src="<%= avatar %>">
        </a>
        <a target="_blank" href="/user/home/<%= uid %>">
            <span class="photo-item-author">
                <%= nickname %>
            </span>
        </a>    
        <span class="photo-item-created">
            <%= update_time %>
        </span>
    </div>
    
    <div style="display:none" class="photo-item-header new-item-header">
        <a target="_blank" href="/user/home/<%= uid %>">
            <img src="<%= avatar %>">
        </a> 
        <span class="new-item-info">
            <span class="new-item-user"><%= nickname %></span>
            <span class="new-item-created"><%= update_time %></span>    
        </span>
        <span class="download-action">
            <span class="download-btn">下载原图</span>
            <span class="icon-download bg-sprite"></span>    
        </span>    
    </div>

    <div class="photo-item-content">
        <a target="_blank" href="/ask/show/<%= ask_id %>">
            <img src="<%= image_url %>">
        </a>
        <div class="photo-item-reply">
            <div class="photo-item-reply-work">
                <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg">
            </div>
        </div>
    </div>
    <div class="ask-content">
            帮我P帅气点
    </div>
    <div class="photo-item-actionbar">
        <span class="bg-sprite icon-like"></span>
        <span class="actionbar-like-count"><%= up_count %></span>
        <span class="bg-sprite icon-comment"></span>
        <span class="actionbar-comment-count"><%= comment_count %></span>

        <span class="actionbar-share-icons">
            <span class="actionbar-share-weibo bg-sprite icon-weibo"></span>
            <span class="actionbar-share-moments bg-sprite icon-moments"></span>            
            <span class="actionbar-share-wechat bg-sprite icon-wechat"></span>
        </span>
    </div>
    <div class="photo-item-replies">
        <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg"> 
        <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg">
        <span class="reply-count"><%= reply_count %>人P过</span> 
    </div>
</div>
</script>

<script type="text/javascript" src="/main/js/index/index.js"></script>

@endsection
