define(['app/views/Base', 'app/models/User', 'tpl!app/templates/LoginView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
                var self = this;
                
                $(".login-popup").fancybox({
                    afterShow: function(){
                        $("#login_btn").click(self.login);
                        $(".register-btn").unbind().bind("click",self.login);
                        $('.login-panel input').keyup(self.keyup);
                        $('#login_password').keypress(self.keypress);
                    }
                });

            },
            keypress:function(e) {
                if(e.which == 13) {
                 $(".register-btn").click(); 
             }
            },
            keyup:function() {
                console.log(123);
                var username = $('#login_name').val();
                var password = $('#login_password').val();
                if( password != '' && username != '' ) {
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
                    $('#user_empty_reminder').removeClass('hide').show().fadeOut(1500);
                    return false;
                } 
                if (password == '') {
                    $('#user_password_reminder').removeClass('hide').show().fadeOut(1500);
                    return false;
                }
                var user = new User;
                user.url  = "/user/login";
                var data = {
                    'username': username,
                    'password': password
                };
                user.fetch({
                    data: data, 
                    success:function(obj, data){
                        if( data.ret === 1 ){   
                            location.href = '/#askflows';
                            location.reload(); 
                        }
                        else {
                            // alert(data.info);
                        }
                    }
                });
            }
        });
    });
