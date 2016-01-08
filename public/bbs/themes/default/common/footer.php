<div class="hdie" style="display:none">
<?php
include THEMEPATH.'../res/app/templates/register/LoginView.html';
include THEMEPATH.'../res/app/templates/register/RegisterView.html';
?>
</div>

<link rel="stylesheet" type="text/css" href="/res/libs/fancybox/jquery.fancybox.css" > 
<script src="/res/lib/fancybox/jquery.fancybox.js"></script>
<script src="/res/lib/common.js"></script>
<script>
$("a[href='#login-popup']").fancybox({
    afterShow: function(){
        $("#login_btn").unbind('click').bind('click', account.login);
        $('.login-panel input').unbind('keyup').bind('keyup', account.login_keyup);
        //$(".register-btn").click(account.login);
        //$('.login-panel input').keyup(account.login_keyup);
    }
});
$(".register-popup").fancybox({
    afterShow: function(){
        $(".sex-pressed").unbind('click').bind('click', account.optionSex);
        //$(".register-btn").click(self.register);
        $('.register-panel input').unbind('keyup').bind('click', account.register_keyup);
    }
});
</script>
<?php
/*

<footer class="small">
	<div class="container">
		<div class="row">
			<?php if($page_links){?>
			<ul class="list-inline">
			<?php foreach($page_links as $key=>$v){?>
			<?php if($v['go_url']){?>
			<li><a href="<?php echo $v['go_url'];?>" target=_blank><?php echo $v['title'];?></a></li>
			<?php } else{?>
			<li><a href="<?php echo site_url('page/index/'.$v['pid']);?>"><?php echo $v['title'];?></a></li>
			<?php }?>
			 <?php }?>
			</ul>
			<?php }?>
			<p><?php echo $settings['site_name']?>  <?php echo $settings['site_stats']?></p>
			<p>Powered by <a href="<?php echo $this->config->item('sys_url');?>" class="text-muted" target="_blank"><?php echo $this->config->item('sys_name');?></a>
<?php echo $this->config->item('sys_version');?> 2013-2014 Some rights reserved 页面执行时间:  {elapsed_time}s</p>
		</div>
	</div>
</footer>
<script src="<?php echo base_url('static/common/js/bootstrap.min.js')?>"></script>
 */
?>
