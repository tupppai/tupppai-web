@section('content')
<head>
	<link rel="stylesheet" href="/main/css/test.css" type="text/css" >
</head>
<div class="photo-container-admin">
    <div class="photo-item-header">
    	<span class="right-head-portrait">
    		<img src="<% avatar %>" alt="">
    	</span>
    	<span class="left-item-message">
    		<span class="personage-message">
    			<span class="user-id">用户ID:<i><%= uid %></i></span>
    			<span class="user-nickname">昵称:<i><%= nickname %></i></span>
    		</span>
    		<span class="photo-varsion-facility">
    			<span class="photo-version">版本:<i><%= 1 %></i></span>
    			<span class="photo-facility">设备:<i><%= 2 %></i></span>
    		</span>
    	</span>
    </div>
    <div class="set-item-actionbar">
    	<span class="set-option">
	    	<select name="" id="">
	    	</select>
    	</span>
    	<span class="set-value">
    		<span>用户屏蔽</span>
    		<span class="cancel-user-recommend">取消用户推荐</span>
    	</span>
    </div>
    <div class="photo-main">
        <span class="item-picture">
            <img src="<%= uploads[0].image_url %>" alt="">
            <span class="small-picture">
                <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150902-06442155e69ac5a6e75.jpg" alt="">
            </span>
        </span>
        <span class="photo-description">
            <% desc %>
        </span>
    </div>
    <div class="card-message">
    	<span class="card-created-time"><%= create_time %></span>
    	<span class="card-created-id">帖子ID:<%= id %></span>
    </div>
    <div class="foot-item-actionbar">
    	<span class="photo-item-count">
	    	<span class="photo-replies-count">作品:<i><%= 2 %></i></span>
	    	<span class="photo-load">下载:<i><%= 1 %></i></span>
    	</span>
	    	<a href=""><span class="photo-detail">图片详情页</span></a>
        <span class="foot-item-value">
            <span class="reply-commend">评论:<i><%= 4 %></i></span>
            <span class="reply-like">点赞:<i><%= 3 %></i></span>
            <span class="shield-cantent">屏蔽内容</span>
            <span class="cancel-hot-recommend">取消热门推荐</span>
        </span>
    </div>
</div>
<div class="clear"></div>
@endsection
