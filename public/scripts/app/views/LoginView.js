define(['app/views/Base', 'tpl!app/templates/LoginView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            template: template,
            events: {
            	"click #login_btn" : "login",
            },
            construct: function () {
                var self = this;
            },
            login: function() {
        	  /**
			    * 登录代码
			    * @author brandwang
			    */
			    $('#login_btn').click(function() {
			        var username = $('#login_name').val();
			        var password = $('#login_password').val();
			        
			        if (username == '') {
			            alert('!');   
			        } else if (password == '') {
			            alert('?');    
			        } else {
			            var url  = "/user/login";
			            var data = {
			                'username': username,
			                'password': password
			            };
			            psAjax(url, 'POST', data, function(data) {
			                var loginModal = $('[data-remodal-id=login-modal]').remodal();
			                if (loginModal.getState() == 'opened') {
			                    loginModal.close();    
			                }
			                //登录成功之后刷新页面
			                window.location.reload();
			            });
			        }
			    }); 
            }
        });
    });
