define(['app/views/Base', 'app/models/User', 'tpl!app/templates/register/RegisterView.html'],
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

                var phone_lenght = phone.length;
                if( phone_lenght != 11 ) {
                    alert( '手机号必须是11位' );
                    return false;
                }
                if( nickname == '') {
                    $('#nickname_empty').removeClass('hide').show().fadeOut(1500);
                	return false;
                }
                if( phone == '') {
                    $('#photo_empty').removeClass('hide').show().fadeOut(1500);
                	return false;
                }
                if( password == '') {
                    $('#password_empty').removeClass('hide').show().fadeOut(1500);
                	return false;
                }
                var url = "/user/register";
                var postData = {
                	'nickname': nickname,
                    'sex' : sex,
                	'mobile': phone,
                	'password': password,
                    'avatar' : avatar
                };
                $.post(url, postData, function( returnData ){
                    var info = returnData.info;
                    alert( info );
                    // if( info = "用户名已存在") {
                    //     alert('手机号已存在'); 
                    //     return false;
                    // }
                    if( returnData.ret == 1 ) {
                        console.log(returnData.ret);
                        window.location.reload()
                    } 
                });
            },
            optionSex: function(event) {
            	$('.sex-pressed').removeClass('boy-pressed').removeClass('girl-pressed');
            	$(event.currentTarget).addClass('boy-pressed');
            	$(event.currentTarget).addClass('girl-pressed');

            }
        });
    });
