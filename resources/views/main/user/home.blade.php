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
					<img src="http://7u2spr.com1.z0.glb.clouddn.com/20150728-15144955b72be936de0.jpg" alt="头像">
				</span>
				<span class="personage-name">remy黄</span>
				<ul class="personage-actionbar-count">
					<li class="personage-attention">
						<i>123</i>
						<span class="personage-attention">关注</span>
					</li>
					<li class="personage-fans">
						<i>123</i>
						<span>粉丝</span>
					</li>
					<li class="personage-link">
						<i>123</i>
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
				<!-- <span class="personage-special-column">
					<span class="border-nav"></span>
					<span class="special-column-icon bg-sprite left"></span>
					<span class="special-column left">专栏</span>
					<span class="special-column-count right">123</span>
				</span> -->
				<span class="personage-seek-help">
					<span class="border-nav"></span>
					<span class="seek-help-icon bg-sprite left"></span>
					<span class="seek-help left">求P</span>
					<span class="seek-help-count right">123</span>
				</span>
				<span class="personage-photo-product">
					<span></span>
					<span class="photo-product-icon bg-sprite left"></span>
					<span class="photo-product left">作品</span>
					<span class="photo-product-count right">123</span>
				</span>
				<span class="personage-under-way">
					<span></span>
					<span class="under-way-icon bg-sprite left"></span>
					<span class="under-way left">进行中</span>
					<span class="under-way-count right">123</span>
				</span>
			</div>
		</div>
		<!-- 个人页面右边 进行中 图片详情 -->
		<div class="under-way-container left">
			<!-- section -->
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
		         <div class="uploading-delete">
		         	<span class="uploading-icon bg-sprite left"></span>
		         	<span class="uploading left">上传作品</span>
		         	<span class="delete right">删除</span>
		         </div>
			</div>
		</div>
	</div>
</div>
@endsection
