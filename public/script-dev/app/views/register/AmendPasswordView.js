define(['app/views/Base', 'app/models/User', 'tpl!app/templates/register/AmendPasswordView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
                var self = this;
                $(".amend-popup").fancybox({
                    afterShow: function() {
                        $('#amend_pwd').unbind('click').bind('click',self.changePassword);
                        $('.amend-main input').keyup(self.keyup);
                    }
                });
            },
            keyup:function() {
                var user_phone = $('input[name=user_phone]').val();
                var user_oldPassword = $('input[name=user_oldpassword]').val();
                var user_newPassword = $('input[name=user_newpassword]').val();
                var user_anewPassword = $('input[name=user_anewpassword]').val();
              
                if(user_phone != '' && user_oldPassword != '' && user_newPassword != '' && user_anewPassword != '' ) {
                    $('.amend_pwd').removeAttr('disabled').addClass('bg-btn');
                }
                if(user_phone == '' || user_oldPassword == '' || user_newPassword == '' || user_anewPassword == '' ) {
                    $('.amend_pwd').attr("disabled", true).removeClass('bg-btn');
                }
            },
            changePassword:function() {
                var phone = $('input[name=user_phone]').val();
                var oldPassword = $('input[name=user_oldpassword]').val();
                var newPassword = $('input[name=user_newpassword]').val();
                var anewPassword = $('input[name=user_anewpassword]').val();
                if( phone == '') {
                    alert('手机号不能为空');
                    return false;
                }
                if( oldPassword == '') {
                    alert('旧密码不能为空');
                    return false;
                }
                if( newPassword == '' || anewPassword == '') {
                    alert('密码不能为空');
                    return false;
                }
                if( newPassword != anewPassword) {
                    alert('两个密码不相同');
                    return false;
                }
            

                var url = "/user/updatePassword";
                var postData = {
                    'old_pwd': oldPassword,
                    'new_pwd': newPassword
                };

                $.post(url, postData, function( returnData ){
                    console.log(returnData);
                });

            }
        });
    });
