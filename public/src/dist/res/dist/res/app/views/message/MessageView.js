define(['app/views/Base', 'tpl!app/templates/message/MessageView.html'],
         
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .message .nav' : 'switchNav',
                'click .message-receive' : 'sendComment',
                'click .message-issue' : 'sendComment',
            },
            construct: function() {
                $("a.menu-bar-item").removeClass('active');
                this.listenTo(this.model, "change", this.render);
            },
            onRender: function() {
                setTimeout(function() {
                    $(".header-back").addClass("height-reduce");
                },1000);
            },
            switchNav: function(e) {
                var self = this;
                var type = $(e.currentTarget).attr('data');
             
                location.href = '/#message/' + type;
            },
            sendComment: function(e) {
                if($(e.currentTarget).hasClass("message-receive")) {
                    window.location.replace("#message/comment");
                    setTimeout(function() {

                    $(".title-comment").removeClass("blo").siblings("span").addClass("blo");
                    $(".message-issue").removeClass("nav-change").siblings("span").addClass("nav-change");
                    }, 500)
                };
                if($(e.currentTarget).hasClass("message-issue")) {
                    window.location.replace("#message/send_comment");
                    setTimeout(function() {

                    $(".title-comment").removeClass("blo").siblings("span").addClass("blo");
                    $(".message-receive").removeClass("nav-change").siblings("span").addClass("nav-change");
                    }, 500)
                }
            }
        });
    });
