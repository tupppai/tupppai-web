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
                        $(".register-btn").click(self.login);
                    }
                });

            },
            login: function(e) {
                var self = this;
                var username = $('#login_name').val();
                var password = $('#login_password').val();

                if (username == '') {
                    alert('!');   
                    return false;
                } 
                if (password == '') {
                    alert('?');    
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
                    success:function(){ 
                        location.href = '#asks';
                        location.reload();
                    }
                });
            },
        });
    });
