define(['app/views/Base', 
        'app/collections/Messages', 
        'app/collections/Replies', 
        'tpl!app/templates/message/MessageView.html',
        'app/views/message/MessagePraiseView',
        'app/views/message/MessageSendLoveView',
    ],
         
    function (View, Messages, Replies, template, MessagePraiseView, MessageSendLoveView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .message .nav' : 'switchNav',
                'click .message-receive' : 'sendComment',
                'click .message-issue' : 'sendComment',
                'click .like-receive' : 'sendLike',
                'click .like-issue' : 'sendLike',
            },
            construct: function() {
                $("a.menu-bar-item").removeClass('active');
                this.listenTo(this.model, "change", this.render);
            },
            onRender: function() {
                setTimeout(function() {
                    $(".header-back").addClass("height-reduce");
                },1000);
                if($(window).width() < 640) {
                   pageResponse({
                        selectors: '.inner-container',     //模块的类名，使用class来控制页面上的模块(1个或多个)
                        mode : 'auto',     // auto || contain || cover 
                        width : '1250',      //输入页面的宽度，只支持输入数值，默认宽度为320px
                        height : '1350'      //输入页面的高度，只支持输入数值，默认高度为504px
                    })
                }
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
            },        
            sendLike: function(e) {
                if($(e.currentTarget).hasClass("like-receive")) {
                    $("#message-item-list").empty();
                    var messages = new Messages;
                    messages.data.type = 'like';

                    var commentListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
                    var view = new MessagePraiseView({
                        collection: messages 
                    });
                    view.scroll();
                    view.collection.reset();
                    view.collection.loading(this.showEmptyView);
                    commentListRegion.show(view);

                    $(".title-praise").removeClass("blo").siblings("span").addClass("blo");
                    $(".like-issue").removeClass("nav-change").siblings("span").addClass("nav-change");
                };
                if($(e.currentTarget).hasClass("like-issue")) {
                    $("#message-item-list").empty();
                    var reply = new Replies;
                        reply.data.uid = 613;
                        reply.url = '/user/uped';
                        reply.data.page = 0;
                        reply.data.size = 10;
                    var commentListRegion = new Backbone.Marionette.Region({el:"#message-item-list"});
                    var view = new MessageSendLoveView({
                        collection: reply 
                    });
                    view.scroll();
                    view.collection.reset();
                    view.collection.loading(this.showEmptyView);
                    commentListRegion.show(view);
                

                    $(".title-praise").removeClass("blo").siblings("span").addClass("blo");
                    $(".like-receive").removeClass("nav-change").siblings("span").addClass("nav-change");
                }
            }
        });
    });
