define(['app/views/Base', 'app/models/User', 'tpl!app/templates/LoginView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {
                var self = this;
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
                user.url  = "/login";
                user.data = {
                    'username': username,
                    'password': password
                };
                user.fetch({success:function(){ 
                    location.href = '#asks';
                    //登录成功之后刷新页面
                    //window.location.reload();
                }});
            },
        });
    });
