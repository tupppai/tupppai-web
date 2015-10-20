<div class="hdie">
<?php
include THEMEPATH.'../scripts/app/templates/LoginView.html';
include THEMEPATH.'../scripts/app/templates/RegisterView.html';
?>
</div>
<a href="#login-popup" class="login-popup"><span class="login-btn">登录</span></a>
    <link rel="stylesheet" type="text/css" href="/main/css/libs/fancybox/jquery.fancybox.css" > 
<script src="/scripts/lib/fancybox/jquery.fancybox.js"></script>
<script>
$(".login-popup").fancybox({
    afterShow: function(){
        $("#login_btn").click(login.login);
        $(".register-btn").click(login.login);
        $('.login-panel input').keyup(login.keyup);
    }
});
$(".register-popup").fancybox({
    afterShow: function(){
            $(".sex-pressed").click(self.optionSex);
            $(".register-btn").click(self.register);
            $('.register-panel input').keyup(self.keyup);
    }
});

var login = {
    keyup:function() {
        var username = $('#login_name').val();
        var password = $('#login_password').val();
        if(username != '' && password != '' ) {
            $('#login_btn').css('background','#F7DF68');
        }
        if(username == '' || password == '' ) {
            $('#login_btn').css('background','#EBEBEB');
        }

    },
    login: function(e) {
        var self = this;
        var username = $('#login_name').val();
        var password = $('#login_password').val();

        if (username == '') {
            alert('登录账号不能为空');   
            return false;
        } 
        if (password == '') {
            alert('密码不能为空');    
            return false;
        }
        $.post('/user/login', {
            username: username, 
            password: password
        }, function(data) {
            history.go(-1);
        });
    }
}

var register = {
    keyup:function() {
        var nickname = $('#register_nickname').val();
        var phone =  $('#register_photo').val();
        var password = $('#register_password').val();

        if(nickname != '' && phone != '' && password != '' ) {
            $('.register-btn').css('background','#F7DF68');
        }
        if(nickname == '' || phone == '' || password == '' ) {
            $('.register-btn').css('background','#EBEBEB');
        }

    },
    register: function (e) {
        var self = this;

        var boy = $('.boy-option').hasClass('boy-pressed');
        var sex = boy ? 0 : 1;
        var avatar = $('#register-avatar').val();
        var nickname = $('#register_nickname').val();
        var phone =  $('#register_photo').val();
        var password = $('#register_password').val();


        if( nickname == '') {
            alert('昵称不能为空');
            return false;
        }
        if( phone == '') {
            alert('手机号码不能为空');
            return false;
        }
        if( password == '') {
            alert('密码不能为空');
            return false;
        }
        //todo: jq
        var url = "/user/save";
        var postData = {
            'nickname': nickname,
            'sex' : sex,
            'phone': phone,
            'password': password,
            'avatar' : avatar
        };
        $.post(url, postData, function( returnData ){
            console.log(returnData);
        });
    },
    optionSex: function(event) {
        $('.sex-pressed').removeClass('boy-pressed').removeClass('girl-pressed');
        $(event.currentTarget).addClass('boy-pressed');
        $(event.currentTarget).addClass('girl-pressed');
    }
}
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
