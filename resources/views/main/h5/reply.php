<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
	<link rel="stylesheet" media="screen and (max-width: 640px)" href="/css/share_reply.css">
	<link rel="stylesheet" media="screen and (min-width: 641px)" href="/css/reply_maxshare.css">
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no, email=no" />
	<title><?php echo $reply['desc'] ?></title>
</head>
<body>
	<div class="share-reply-container">
		<div class="reply-content">
		<section class="header-section">
			<span class="header-portrait">
				<a class="max-block" href="http://www.tupppai.com/#index">
					<img src="<?php echo $reply['avatar'] ?>" alt="">
				</a>
				<a class="min-none">
					<img src="<?php echo $reply['avatar'] ?>" alt="">
				</a>
			</span>
			<span class="personage-message">
				<span class="name"><?php echo $reply['nickname'] ?></span>
			</span>
		</section>
		<section class="picture">
				<a class="max-block" href="http://www.tupppai.com/#index">
					<img src="<?php echo $reply['image_url'] ?>" alt="">
				</a>
				<a class="min-none">
					<img src="<?php echo $reply['image_url'] ?>" alt="">
				</a>


            <div class="reply-picture">
                <?php foreach($reply['ask_uploads'] as $ask) { ?>
				<span>
				<i class="bookmark">原图</i>
				<a class="max-block" href="http://www.tupppai.com/#index">
					<img src="<?php echo $ask['image_url'] ?>" alt="">
				</a>
				<a class="min-none">
					<img src="<?php echo $ask['image_url'] ?>" alt="">
				</a>
                </span>
                <?php } ?>
			</div>
        </section>
        <div class="share-reply-desc" ><?php echo $reply['desc'] ?></div>
		</div>
		<section class="footer-reply">
			<div class="tupai-description">
				<span class="code-remind">
					<span class="remind-content-1">长按识别二维码,</span>
					<span class="remind-content-2">看更多让你意想不到的图片</span>
				</span>
				<span class="code-picture">
					<img src="/main/img/downloadQrcode.png" alt="">
				</span>
			</div>
			<div class="share-for-tupai">
				<span class="share-for">分享自</span>
				<span class="tupai-icon">
					<img src="../../../main/img/logo.jpg" alt="">
				</span>
				<span class="tupai-name">图派</span>
			</div>
		</section>
	</div>
</body>
</html>
