define(['tpl!app/views/common/header/header.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .login-tupppai": 'fnLoginPopup',
                "click .resgister-tupppai" : 'fnResgister',
                "click .ask-button" : 'fnBinding',
                "click .header-nav li" : 'fnPress',
                "click #exit-out" : "fnExitOut"
            },
            onShow: function() {
              $("a#Binding-popup").fancybox({
                    'padding': 0
                });

              $('.login-button').click(function(){
                    var username = $('.fancybox-inner .phone-number input').val();
                    var password = $('.fancybox-inner .password input').val();
                    var url = '/user/login'
                    $.post( url,{
                        username: username ,
                        password: password
                    } ,function(data){
                        if(data.uid) {
                            debugger;
                           history.back();
                           window.location.reload();
                        }

                    })
                })
            },
            fnExitOut:function() {
                $.get('/user/logout',{},function(){
                        window.location.reload();
                })
            },
            fnPress:function(e) {
                $('.header-nav li').removeClass('home-press');
                $(e.currentTarget).addClass('home-press');
            },
            fnLoginPopup:function() {
                $("a[href=#login-popup]").attr('id','login-popup');

                $("a#login-popup").fancybox({
                        'padding': 0,
                    });
                $("a#login-popup").click();
                $("a#login-popup").removeAttr('id');
            },
            fnResgister: function() {
              $("a[href=#resgister-popup]").attr('id','resgister-popup');
              $("a#resgister-popup").fancybox({
                    'padding': 0
                });
                $("a#resgister-popup").click();
                $("a#resgister-popup").removeAttr('id');
            },
            fnBinding: function() {
                $("a#Binding-popup").click();
            }

        });
    });
