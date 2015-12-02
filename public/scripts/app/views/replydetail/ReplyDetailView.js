define([
        'app/views/Base', 
        'app/models/Base',
        'app/models/Ask', 
        'app/models/Like',
        'tpl!app/templates/replydetail/ReplyDetailView.html'
       ],
    function (View, ModelBase, Ask, Like, template) {
        "use strict"
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .written-reply": 'answer',
                "click #reply-left" : 'replyChange',
                "click #reply-right" : 'replyChange',
                "click .other-pic img" : 'replyChange',
                "click .like_toggle" : 'likeToggle',
            },
            construct: function() { 
                this.listenTo(this.model, 'change', this.render);
                //this.collection.loading();
            },
            loadComment: function(replyid) {
                var comment = new Comments;
                var replyCommentView = new Backbone.Marionette.Region({el:"#replyCommentView"});
                var view = new ReplyCommentView({
                    collection: comment
                });
                replyCommentView.show(view);
            },
            answer : function(e) {
                $(e.currentTarget).css({
                    display: "none"
                }).siblings(".reply-inp").css({
                    display: "block"
                }).parents(".comment-content").siblings().children(".reply-inp").css({
                    display: "none"
                }).siblings(".written-reply").css({
                    display: "block"
                })
            },
            replyChange : function(e) {
                var replyImg = $(".other-pic img");
                var replyLength = replyImg.length;
                var replyIndex = parseInt($(".other-pic").attr("otherNum"));
                var replySrc = null;

                if(e.currentTarget.id == "reply-right") {
                    replyIndex++;
                    if (replyIndex >= replyLength) {
                        replyIndex = 0;
                    };
                    $(".other-pic").attr("otherNum", replyIndex)
                } 
                if(e.currentTarget.id == "reply-left") {
                    replyIndex--;
                    if (replyIndex < 0) {
                        replyIndex = replyLength;
                    };
                    $(".other-pic").attr("otherNum", replyIndex)
                }
                if(e.currentTarget.id == "other-pho") {                    
                    replyIndex = $(e.currentTarget).index();    
                    $(".other-pic").attr("otherNum", replyIndex)
                }
                replySrc = replyImg.eq(replyIndex).attr("src");
                $(".main-pic").attr("src", replySrc);
                replyImg.eq(replyIndex).addClass("img-change").siblings("img").removeClass("img-change");
            },
        });
    });
