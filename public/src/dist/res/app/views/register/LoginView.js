define(['app/views/Base', 'app/models/User', 'tpl!app/templates/register/LoginView.html'],
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
                        $("#login_btn").unbind().bind("click",account.login);
                        $('.login-panel input').keyup(account.login_keyup);
                        $('#login_password').keypress(account.keypress);
                    }
                });
            },
        
        });
    });
