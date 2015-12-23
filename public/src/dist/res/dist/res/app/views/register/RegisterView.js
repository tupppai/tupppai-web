define(['app/views/Base', 'app/models/User', 'tpl!app/templates/register/RegisterView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () { 
                var self = this;

                $(".register-popup").fancybox({
                    afterShow: function(){
                        $('#send_register_code').unbind('click').bind('click',self.countdown);
                        $(".sex-pressed").unbind('click').bind('click',self.optionSex);
                        $(".register-btn").unbind('click').bind('click',account.register);
                        $('.register-panel input').keyup(account.register_keyup);
                        $("#Limit_btn").unbind('click').bind('click', account.login);
                    }
                });

            },
            countdown:function() {
                var util = {
                    wait: 60,
                    hsTime: function (that) {
                        console.log(that);
                        var self = $(this);
                        var wait = $(that).val();
                        wait = wait.slice(0,-1);
                        self.addClass('sent');

                        if (wait == 0) {
                            $('#send_register_code').removeAttr("disabled").removeClass('sent').val('重新发送');
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
                var phone    =  $('#register_phone').val();
                var phone_lenght = phone.length;
                if( phone_lenght != 11 ) {
                    alert( "你的手机号码位数不对" );
                }else {
                    var phone = $('#register_phone').val();
                    var url = "/user/code?phone="+phone;
                    $.get(url, function( returnData ){
                        console.log(returnData);
                    });
                    util.hsTime('#send_register_code');
               }
            },
    
            optionSex: function(event) {
            	$('.sex-pressed').removeClass('boy-pressed').removeClass('girl-pressed');
            	$(event.currentTarget).addClass('boy-pressed');
            	$(event.currentTarget).addClass('girl-pressed');

            }
        });
    });
