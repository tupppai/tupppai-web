define([
        'app/views/Base',
        'app/models/Base',
        'app/models/Ask', 
        'app/models/Reply',
        'app/collections/Comments',
        'tpl!app/templates/replydetailplay/ReplyDetailPlayView.html',
        'app/views/replydetailplay/ReplyDetailPersonView',
        'app/views/replydetailplay/ReplyDetailCommentView',
        'app/views/replydetailplay/ReplyDetailCountView',
       ],
    function (View, ModelBase, Ask, Reply, Comments, template, ReplyDetailPersonView, ReplyDetailCommentView, ReplyDetailCountView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
           
            events: {
                "click .other-click" : "otherPerson",
                "click .old-click" : "oldPerson",
                "click .center-loading-image-container" : "picScroll",
                "click #replyDetailRight" : "picScroll",
                "click #replyDetailLeft" : "picScroll",
            },
            picScroll: function(e) {
                var replyImg = $(".center-loading-image-container img");  //获取img
                var replyLength = replyImg.length; //获取img长度
                var replyIndex = parseInt($(".detail-pic").attr("otherNum")); //获取索引值
                // var dataIdx = parseInt($(e.currentTarget).attr("data-idx"));    //  
                var replySrc = null;
                var picIndex = null;
                if(e.currentTarget.id == "replyDetailRight") {
                    replyIndex++;
                    if (replyIndex >= (replyLength - 1)) {
                        replyIndex = (replyLength - 1);
                    };
                    $(".detail-pic").attr("otherNum", replyIndex);
                };
                if(e.currentTarget.id == "replyDetailLeft") {
                    replyIndex--;
                    if (replyIndex <= 0) {
                        replyIndex = 0;
                    };
                    $(".detail-pic").attr("otherNum", replyIndex);
                };  

                 // 点击作品
                if($(e.currentTarget).hasClass("center-loading-image-container")) {     
                    replyIndex = $(e.currentTarget).index();
                    $(".detail-pic").attr("otherNum", replyIndex);
                };

                replySrc = replyImg.eq(replyIndex).attr("src"); //获取当前图片的src
                $("#bigPic").attr("src", replySrc);

                replyImg.eq(replyIndex).parents(".center-loading-image-container").addClass("change-pic").siblings(".center-loading-image-container").removeClass("change-pic");
                $(".original-pic").removeClass("original-change");

                if (replyIndex == (replyLength - 1)) {
                    $(".replyDetailRight").css({
                        display: "none"
                    })
                } else {
                    $(".replyDetailRight").css({
                        display: "block"
                    })
                };
                 if (replyIndex == 0) {
                    $(".replyDetailLeft").css({
                        display: "none"
                    })
                } else {
                     $(".replyDetailLeft").css({
                        display: "block"
                    })
                };

                var dataIdx = replyIndex + 1;
                if (parseInt($(".detail-pic").css("marginLeft")) == 0)  {
                    picIndex = 3;
                };
                if (dataIdx > picIndex && dataIdx < replyLength && dataIdx >= 3) {
                    $(".detail-pic").animate({
                        marginLeft: - 90 * (dataIdx - 3) + "px"
                    }, 400);
                    picIndex = dataIdx;
                };

                var reply_id = replyImg.eq(replyIndex).parents(".center-loading-image-container").attr('data-id');
                var type = 2;

                $("#replyComment_btn").attr("data-id",reply_id);
                $("#replyComment_btn").attr("data-type", type);

                var model = new Reply;
                model.url = '/replies/' + reply_id;
                model.fetch();

                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyDetailPersonView = new Backbone.Marionette.Region({el:"#replyDetailPersonView"});
                var view = new ReplyDetailPersonView({
                    model: model
                });
                replyDetailPersonView.show(view); 

                var userIfo = new Backbone.Marionette.Region({el:"#userIfo"});
                var view = new ReplyDetailCommentView({
                    collection: comments
                });
                userIfo.show(view); 

                var count = new Backbone.Marionette.Region({el:"#count"});
                var view = new ReplyDetailCountView({
                    model: model
                });
                count.show(view); 






            },
            otherPerson: function(e) {   

                var reply_id = $(e.currentTarget).attr('data-id');
                var type = 2;

                $("#replyComment_btn").attr("data-id",reply_id);
                $("#replyComment_btn").attr("data-type", type);

                var model = new Reply;
                model.url = '/replies/' + reply_id;
                model.fetch();

                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyDetailPersonView = new Backbone.Marionette.Region({el:"#replyDetailPersonView"});
                var view = new ReplyDetailPersonView({
                    model: model
                });
                replyDetailPersonView.show(view); 

                var userIfo = new Backbone.Marionette.Region({el:"#userIfo"});
                var view = new ReplyDetailCommentView({
                    collection: comments
                });
                userIfo.show(view); 

                var count = new Backbone.Marionette.Region({el:"#count"});
                var view = new ReplyDetailCountView({
                    model: model
                });
                count.show(view); 
            },
            oldPerson: function(e) {   

                var reply_id = $(e.currentTarget).attr('data-id');
                var type = 2;

                $("#replyComment_btn").attr("data-id",reply_id);
                $("#replyComment_btn").attr("data-type", type);

                var model = new Reply;
                model.url = '/replies/' + reply_id;
                model.fetch();

                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyDetailPersonView = new Backbone.Marionette.Region({el:"#replyDetailPersonView"});
                var view = new ReplyDetailPersonView({
                    model: model
                });
                replyDetailPersonView.show(view); 

                var userIfo = new Backbone.Marionette.Region({el:"#userIfo"});
                var view = new ReplyDetailCommentView({
                    collection: comments
                });
                userIfo.show(view); 

                var count = new Backbone.Marionette.Region({el:"#count"});
                var view = new ReplyDetailCountView({
                    model: model
                });
                count.show(view); 
            },
            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
        });
    });
