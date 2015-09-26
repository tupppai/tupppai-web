define(['app/views/Base', 'app/models/User', 'tpl!app/templates/LoginView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
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
                    success:function(data1, data2){ 
                        self.loginModal = $('[data-remodal-id=login-modal]').remodal();
                        self.loginModal.close();
                        location.reload();
                    }
                });
            },
        });
    });
