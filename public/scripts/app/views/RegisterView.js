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
                var user = new User;
                user.url = "/user/save";
                var postData = {
                	'nickname': nickname,
                    'sex' : sex,
                	'phone': phone,
                	'password': password,
                    'avatar' : avatar
                };
                $.post(user.url, postData, function( returnData ){
                    console.log(returnData);
                });
            },
            optionSex: function(event) {
            	$('.sex-pressed').removeClass('boy-pressed').removeClass('girl-pressed');
            	$(event.currentTarget).addClass('boy-pressed');
            	$(event.currentTarget).addClass('girl-pressed');

            }
        });
    });
