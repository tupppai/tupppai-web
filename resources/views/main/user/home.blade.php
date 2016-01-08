@section('content')
<head>
	<link rel="stylesheet" href="/main/css/home.css" type="text/css">
</head>



<div class="inner-container">
	<div class="personage-container">
	<!-- 个人页面的左侧 -->
		<div class="personage-message left">
			<div class="personage-header">
				<span class="home-page-icon bg-sprite"></span>
				<span class="return-home-page">返回首页</span>
			</div>
			<!-- 个人页面main -->
			<div class="personage-details">
				<span class="personage-head-protrait">
					<img src="{{ $user['avatar'] }}" alt="{{ $user['username']}}">
				</span>
				<span class="personage-name">{{ $user['username']}}</span>
				<ul class="personage-actionbar-count">
					<li class="personage-attention">
						<i>{{ $user['fellow_count'] }}</i>
						<span class="personage-attention">关注</span>
					</li>
					<li class="personage-fans">
						<i>{{ $user['fans_count'] }}</i>
						<span>粉丝</span>
					</li>
					<li class="personage-link">
						<i>{{ $user['uped_count'] }}</i>
						<span>点赞</span>
					</li>
				</ul>
				<span  class="toggle-attention">
					<span class="cancel-attention">取消关注</span>
					<span class="attention">+关注</span>
				</span>
			</div>
			<!-- 个人页面nav -->
			<div class="personage-nav">
				<span class="personage-seek-help">
					<span class="border-nav hide"></span>
					<span class="seek-help-icon bg-sprite left"></span>
					<span class="seek-help left">求P</span>
					<span class="seek-help-count right">{{ $user['ask_count'] }}</span>
				</span>
				<span class="personage-photo-product">
					<span class="border-nav hide"></span>
					<span class="photo-product-icon bg-sprite left"></span>
					<span class="photo-product left">作品</span>
					<span class="photo-product-count right">{{ $user['reply_count'] }}</span>
				</span>
				<span class="personage-under-way">
					<span class="border-nav hide"></span>
					<span class="under-way-icon bg-sprite left"></span>
					<span class="under-way left">进行中</span>
					<span class="under-way-count right">{{ $user['inprogress_count'] }}</span>
				</span>
			</div>
		</div>
		<!-- 个人页面右边 进行中 图片详情 -->
		<!-- section -->
		<div class="under-way-container left">
		</div>
	</div>
</div>


<script type="text/template" id="home-ask-template">
    <!-- 求P -->
    <div class="photo-item left">
        <div class="photo-item-content">
            <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg" alt="">
            <div class="photo-item-reply">
                <div class="photo-item-reply-work">
                    <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg">
                </div>
             </div>
        </div>
        <div class="ask-tiem-actionbar">
            <span class="download-item-actionbar">
                <span class="download-icon bg-sprite"></span>
                <span class="download-work">下载作品</span>
            </span>
            <span class="work-count right">123个作品</span>
        </div>
    </div>
<script>    
<script type="text/template" id="home-inporgress-template">
    <!-- 进行中 -->
    <div class="photo-item left">
        <div class="photo-item-content">
            <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg" alt="">
            <div class="photo-item-reply">
                <div class="photo-item-reply-work">
                    <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg">
                </div>
             </div>
        </div>
        <div class="photo-item-personage">
            <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg">
            <span class="photo-item-author">
                陈晨
            </span>
            <span class="photo-item-created">
                2小时前
            </span>
         </div>
         <div class="uploading-delete" data-remodal-target="uploading-modal" href="#uploading-modal">
            <span class="uploading-icon bg-sprite left"></span>
            <span class="uploading left">上传作品</span>
            <span class="delete right">删除</span>
         </div>
    </div>
<script>    
<script type="text/template" id="home-reply-template">
    <!-- 作品页面 -->
    <div class="photo-item left">
        <div class="photo-item-content">
            <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg" alt="">
            <div class="photo-item-reply">
                <div class="photo-item-reply-work">
                    <img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg">
                </div>
             </div>
        </div>
         <div class="operate-item-actionbar">
            <!-- <span class="check">审核中</span>
            <span class="delete right">删除</span> -->
            <span class="grade right">评分:<i>1</i></span>
            <!-- <span class="refuse-content right">效果不好</span> -->
         </div>
    </div>
</script>

<script type="text/javascript" src="/main/js/user/home.js"></script>

<!-- clear float -->
<div class="clear"></div>
@endsection
