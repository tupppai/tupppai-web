<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
	<link rel="stylesheet" href="/css/h5.css">
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no, email=no" />
    <title><?php echo $ask['desc'] ?></title>
</head>
<body>
	<div class="share-container">
		<section class="header-section">
			<span class="header-portrait">
                <img src="<?php echo $ask['avatar'] ?>" alt="">
			</span>
			<span class="personage-message">
                <span class="name"><?php echo $ask['nickname'] ?></span>
                <span class="create-time"><?php echo date('Ymd H:i:s', $ask['create_time']) ?></span>
			</span>
		</section>
		<section class="picture">
            <img src="<?php echo $ask['image_url'] ?>" alt="">
		</section>
		<section class="describe-content">
            <span class="content">
                <?php echo $ask['desc'] ?>
			</span>
		</section>
		<section class="actionbar-container">
			<span class="comment-message">
				<i class="comment-icon spriteH5"></i>
                <i class="comment-count"><?php echo $ask['comment_count'] ?></i>
			</span>
			<span class="share-message">
				<i class="share-icon spriteH5"></i>
				<i class="share-count">1<?php echo $ask['share_count'] ?></i>
			</span>
			<i class="p-icon spriteH5"></i>
		</section>
		<section>
            <div class="comment-container">
                <?php foreach($ask['hot_comments'] as $comment) { ?>
				<div class="comment-head">
					<span  class="head-portrait">
                        <img src="<?php echo $comment['avatar'] ?>" alt="">
					</span>
					<span class="comment-message">
                        <span class="comment-name"><?php echo $comment['nickname'] ?></span>
                        <span class="comment-content"><?php echo $comment['content'] ?></span>
                        <span class="comment-time"><?php echo date('Ymd H:i:s', $comment['create_time']); ?></span>
					</span>
                </div>
                <?php } ?>
			</div>
		</section>
		<section class="all-comment">
			<div class="look-over-comment">查看所有评论</div>
		</section>
		<section class="footer-container">
			<span class="app-logo">
				<img src="../../../main/img/logo.jpg" alt="">
			</span>
			<span class="app-message">
				<span class="app-name">图派</span>
				<span class="app-link">tupppai.com</span>
			</span>
			<span class="app-download-btn">
				<img src="../../../main/img/downloadRemind.jpg" alt="">
			</span>
		</section>

	</div>
</body>
</html>
