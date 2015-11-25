define(['app/views/Base', 'app/models/User', 'tpl!app/templates/register/UserBingdingView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
            	var self = this;
                $(".bingding-popup").fancybox({
                    afterShow: function() {
                        $('#sned_code_Bingding').click(self.countdown);
                        $('#confirm_bingding').click(self.changePassword);
                        $('.bingding-main input').keyup(self.keyup);
                    }
                });
            },
            keyup:function() {
                var phone = $('input[name=bingding-phone]').val();
                var code = $('input[name=bingding-code]').val();
                var password = $('input[name=bingding-password]').val();
   
                if(phone != '' && code != '' && password != ''  ) {
                    $('.confirm-bingding').removeAttr('disabled').addClass('bg-btn');
                }
                if(phone == '' || code == '' || password == '' ) {
                    $('.confirm-bingding').attr("disabled", true).removeClass('bg-btn');
                }
            },
            countdown:function() {
                var util = {
                    wait: 60,
                    hsTime: function (that) {
                        var self = $(this);
                        var wait = $(that).val();
                        wait = wait.slice(0,-1);
                        self.addClass('sent');

                        if (wait == 0) {
                            $('#sned_code_Bingding').removeAttr("disabled").removeClass('sent').val('重新发送');
                            self.wait = 60;
                        } else {
                            var self = this;
                            $(that).attr("disabled", true).addClass('sent').val( + self.wait + 'S');
                            self.wait--;
                            setTimeout(function () {
                                self.hsTime(that);
                            }, 1000)
                        }
                    }
                }
                util.hsTime('#sned_code_Bingding');
            },
            changePassword:function() {
                var phone = $('input[name=phone]').val();
                var code = $('input[name=code]').val();
                var password = $('input[name=password]').val();
                if( phone == '') {
                    alert('手机号不能为空');
                    return false;
                }
                if( code == '') {
                    alert('验证码不能为空');
                    return false;
                }
                if( password == '' ) {
                    alert('密码不能为空');
                    return false;
                }
                //todo: jq
                // var url = "/user/save";
                var postData = {
                    'phone': phone,
                    'code' : code,
                    'password': password,
                };
                $.get(url, postData, function( returnData ){
                    console.log(returnData);
                });
            }
        });
    });
