define([
        'app/views/Base',
        'app/models/Base',
        'app/models/Ask', 
        'app/models/Reply',
        'app/collections/Comments',
        'tpl!app/templates/askdetail/AskDetailPlayView.html',
        'app/views/askdetail/AskDetailPersonView',
        'app/views/askdetail/AskDetailCommentView',
        'app/views/askdetail/AskDetailCountView',
        'app/views/askdetail/AskDetailActionView',
       ],
    function (View, ModelBase, Ask, Reply, Comments, template, ReplyDetailPersonView, ReplyDetailCommentView, ReplyDetailCountView, ReplyDetailActionView) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .like_toggle" : 'likeToggleLarge',
                "click .pic-scroll" : "picScroll",
                "click #askDetailRight" : "picScroll",
                "click #askDetailLeft" : "picScroll",
                "click .reply-play" : "replyBlo",
                "click .reply-more" : "moreScroll", 
                "click #replyCommentBtn" : "replyCommentBtn",
                "click .inp-reply" : "inpReply",
                "click .reply-cancel" : "replyNone",
                "click .download" : "download",
            },
            inpReply: function(e) {
                var el = $(e.currentTarget).siblings('.play-inp');
                var content = el.val();
                var reply_to = el.attr('reply-to');
                var type = el.attr('data-type');
                var comment_id = el.attr('comment-id');
                var target_id = el.attr('target-id');

                var url = "/comments/save";

                var postData = {
                    'content': content,
                    'type' : type,
                    'id': target_id,
                    'reply_to' : reply_to,
                    'for_comment' : comment_id
                };
                $.post(url, postData, function( returnData ){
                    var info = returnData.info;
                    if( returnData.ret == 1 ) {
                        toast('回复评论成功');
                        $('.center-loading-image-container[data-id=' + target_id + ']').trigger("click");
                        // window.location.reload()
                    } 
                });



            },
            replyCommentBtn: function(e) {
                var id = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');
                var content = $("#textInp").val();
                if(!content || content == "") {
                    toast("内容不能为空");
                    return false;
                }
                $.post('/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    if(data.ret == 1){
                        $('.center-loading-image-container[data-id=' + id + ']').trigger("click");
                        //todo: upgrade append
                        $("#textInp").val(' ');
                        var t = $(document);
                        // t.scrollTop(t.height());  
                    }
                    else {
                        $(".login-popup").click();
                    }
                });
            },
            

            moreScroll: function() {
                $(".reply-detail-ifo").scrollTop(204);
                $(".reply-more").addClass("blo");
                $(".reply-detail-ifo").css({
                    overflow: "auto"
                })
            },
            sendComment:function(e) {
                var id = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');
                var content = $(e.currentTarget).prev().val();
                if(!content || content == "") {
                    toast("内容不能为空");
                    return false;
                }
                $.post('/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    if(data.ret == 1){
                        $('.reply-trigger[data-id=' + id + ']').trigger("click");
                        //todo: upgrade append
                        $(".praise-comment textarea").val(' ');
                        var t = $(document);
                        t.scrollTop(t.height());  
                    }
                    else {
                        $(".login-popup").click();
                    }
                });
            },
            replyNone: function(e) {
                $(".inp-frame").addClass("blo");
            },
            replyBlo: function(e) {
                $(e.currentTarget).parents(".reply-ifo").siblings(".inp-frame").removeClass("blo").parents(".user-ifo").siblings(".user-ifo").find(".inp-frame").addClass("blo");
            },

            picScroll: function(e) {
                var replyImg = $(".pic-scroll img");  //获取img
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
                    $("#replyDetailRight").css({
                        display: "none"
                    })
                } else {
                    $("#replyDetailRight").css({
                        display: "block"
                    })
                };
                 if (replyIndex == 0) {
                    $("#replyDetailLeft").css({
                        display: "none"
                    })
                } else {
                     $("#replyDetailLeft").css({
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
                var type = replyImg.eq(replyIndex).parents(".center-loading-image-container").attr("data-type");

                $("#replyCommentBtn").attr("data-id",reply_id);
                $("#replyCommentBtn").attr("data-type", type);

                if(type == 2) {
                    var model = new Reply;
                    model.url = '/replies/' + reply_id;
                    model.fetch();
                    $("#bgIcon").addClass("other-icon").removeClass("old-icon");
                };
                if(type == 1) {
                    var model = new Ask;
                    model.url = '/asks/' + reply_id;
                    model.fetch();
                    $("#bgIcon").addClass("old-icon").removeClass("other-icon");
                };
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

                var action = new Backbone.Marionette.Region({el:"#action"});
                var view = new ReplyDetailActionView({
                    model: model
                });
                action.show(view);

                setTimeout(function(){
                    if($(".reply-comment").height() > 550) {
                        $(".reply-more").removeClass("blo");
                    } else {
                        $(".reply-more").addClass("blo");
                    };
                    $(".reply-detail-ifo").css({
                        overflow: "hidden"
                    });
                }, 700)
            },
            construct: function() {
                var self = this;
                this.listenTo(this.model, 'change', this.render);
            },
        });
    });
