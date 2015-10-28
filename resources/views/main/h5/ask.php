<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
	<link rel="stylesheet" href="../../../main/css/share_ask.css">
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no, email=no" />
	<title><?php echo $ask['desc'] ?></title>
</head>
<body>
	<div class="share-ask-container">
		<div class="ask-content">
		<section class="header-section">
			<span class="header-portrait">
				<img src="<?php echo $ask['avatar'] ?>" alt="">
			</span>
			<span class="personage-message">
                <span class="name"><?php echo $ask['nickname']; ?></span>
			</span>
		</section>
		<section class="picture">
			<img src="<?php echo $ask['avatar'] ?>" alt="">
		</section>
		</div>
		<section class="footer-ask">
			<div class="tupai-description">
				<span class="code-remind">
					<span class="remind-content-1">长按识别二维码,</span>
					<span class="remind-content-2">看更多让你意想不到的图片</span>
				</span>
				<span class="code-picture">
					<img src="../../../main/img/downloadQrcode.png" alt="">
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
