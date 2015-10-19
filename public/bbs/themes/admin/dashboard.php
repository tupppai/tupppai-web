<!DOCTYPE html><html><head><meta content='' name='description'>
<meta charset='UTF-8'>
<meta content='True' name='HandheldFriendly'>
<meta content='width=device-width, initial-scale=1.0' name='viewport'>
<title>运行状态 - 管理后台 - <?php echo $settings['site_name']?></title>
<?php $this->load->view ( 'common/header-meta' ); ?>
</head>
<body id="startbbs">
<?php $this->load->view ( 'common/header' ); ?>
    <div class="container">
        <div class="row">
<?php $this->load->view ('common/sidebar');?>
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">欢迎进入后台管理</h3>
                    </div>
                </div>
                <div class="row">
	                <div class="col-md-6">
		                <div class="panel panel-default">
		                    <div class="panel-heading">
		                        <h3 class="panel-title">统计</h3>
		                    </div>
		                    <div class="panel-body">
			                    <ul class="list-unstyled">
						            <li>最新会员：<?php echo $stats['last_username']?></li>
						            <li>注册会员： <?php echo $stats['total_users']?></li>
						            <li>今日话题： <?php echo $stats['today_topics'];?></li>
						            <li>昨日话题： <?php echo $stats['yesterday_topics'];?></li>
						            <li>话题总数： <?php echo $stats['total_topics']?></li>
						            <li>回复数： <?php echo $stats['total_comments']?></li>
								</ul>
		                    </div>
		                </div>
	                </div>
	                <div class="col-md-6">
		                <div class="panel panel-default">
		                    <div class="panel-heading">
		                        <h3 class="panel-title">清理</h3>
		                    </div>
		                    <div class="panel-body">

		                    </div>
		                </div>
	                </div>
                </div>
            </div><!-- /.col-md-8 -->

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php $this->load->view ( 'common/footer' ); ?>
</body></html>
