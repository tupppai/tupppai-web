define(['app/views/Base', 'app/models/User', 'tpl!app/templates/RegisterView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this;

                $(".register-popup").fancybox({
                    afterShow: function(){
                            $(".sex-pressed").click(self.optionSex);
                            $(".register-btn").click(self.register);
                            $('.register-panel input').keyup(self.keyup);
                    }
                });

            },
            keyup:function() {
                var registerNickname = $('#register_nickname').val();
                var registerPhoto =  $('#register_photo').val();
                var registerPassword = $('#register_password').val();

                if(registerNickname != '' && registerPhoto != '' && registerPassword != '' ) {
                    $('.register-btn').css('background','#F7DF68');
                }
                if(registerNickname == '' || registerPhoto == '' || registerPassword == '' ) {
                    $('.register-btn').css('background','#EBEBEB');
                }

            },
            register: function (e) {
                var self = this;		   
                var registerNickname = $('#register_nickname').val();
                var registerPhoto =  $('#register_photo').val();
                var registerCode = $('#register_code').val();
                var registerPassword = $('#register_password').val();

                if( registerNickname == '') {
                	alert('昵称不能为空');
                	return false;
                }
                if( registerPhoto == '') {
                	alert('手机号码不能为空');
                	return false;
                }
                if( registerCode == '' ) {
                	alert('验证码不能为空');
                	return false;
                }
                if( registerPassword == '') {
                	alert('密码不能为空');
                	return false;
                }
                var user = new User;
                user.url = "/user/login";
                var data = {
                	'registerNickname': registerNickname,
                	'registerPhoto': registerPhoto,
                	'registerCode': registerCode,
                	'registerPassword': registerPassword,
                };
                user.fetch({
                	data: data,
                	seccess:function() {
                		self.registerModal.close();
                		location.reload();
                	}
                })
            },
            optionSex: function(event) {
            	$('.sex-pressed').removeClass('boy-pressed').removeClass('girl-pressed');
            	$(event.currentTarget).addClass('boy-pressed');
            	$(event.currentTarget).addClass('girl-pressed');

            }
        });
    });
