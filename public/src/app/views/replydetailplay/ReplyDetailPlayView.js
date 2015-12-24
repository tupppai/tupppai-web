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
        'app/views/replydetailplay/ReplyDetailActionView',
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
                "click #replyDetailRight" : "picScroll",
                "click #replyDetailLeft" : "picScroll",
                "click .reply-play" : "replyBlo",
                "click .reply-more" : "moreScroll", 
                "click #replyCommentBtn" : "replyCommentBtn",
                "click .inp-reply" : "inpReply",
                "click .reply-cancel" : "replyNone",
                "click .download" : "download",
                "click .super-like" : "superLike",
                "mouseover .icon-add-emoji" : "addEmoji"
            },
            addEmoji: function() {
                $('.icon-add-emoji').emojiSelector({
                    assign: 'textInp',
                    path: '/res/lib/face-selector/face/'
                });
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

                var current_play_icon   = $(e.currentTarget).parents(".reply-ifo").siblings(".inp-frame").find('.play-icon')[0];
                // 保证唯一id标识
                var current_play_id     = $(e.currentTarget).parents(".reply-ifo").siblings(".inp-frame").find(".play-inp").attr('name');
                $(current_play_icon).emojiSelector({
                    assign: current_play_id,
                    path: '/res/lib/face-selector/face/'
                });
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
                replySrc = trimUrl(replySrc);
                debugger;
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
                }, 700);

                var imageWidth  = $("#bigPic").width();
                var imageHeight = $("#bigPic").height();
                var imageRatio  = imageWidth/imageHeight;
                var centerLoadContainer = $("#bigPic").parents('.center-image');
                var containerWidth      = $(centerLoadContainer)[0].offsetWidth;
                var containerHeight     = $(centerLoadContainer)[0].offsetHeight;
                var tempWidth  = 0;
                var tempHeight = 0;
                var offsetLeft = 0;
                var offsetTop  = 0;
                
                if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                    // 图片宽高都大于容器宽高

                    // 图片长比较长，按照高度缩放，截取中间部分
                    if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                      
                        tempWidth = containerWidth;
                        tempHeight = imageHeight * containerWidth / imageWidth;

                        offsetTop = (containerHeight - tempHeight) / 2;
                        offsetLeft = 0;
                    } else if (imageWidth / imageHeight < containerWidth / containerHeight) {
                        //图片比较高，安装宽度缩放，截取中间部分
                        tempHeight = containerHeight;
                        tempWidth  = imageWidth * containerHeight / imageHeight;

                        // tempWidth  = containerWidth;
                        // tempHeight = imageHeight * containerWidth / imageWidth;

                        offsetTop = 0;
                        offsetLeft  = (containerWidth - tempWidth) / 2;
                    };    
                } else if (imageWidth < containerWidth && imageHeight < containerHeight) {
                    // 图片宽高都小于容器宽高
                    if (imageRatio > containerWidth / containerHeight) {
                        tempWidth    = containerWidth;
                        tempHeight   = tempWidth / imageWidth * imageHeight;

                        offsetLeft   = 0;
                        offsetTop    = (containerHeight - tempHeight) / 2;
                    } else {
                        tempWidth    = imageWidth / imageHeight * containerHeight;
                        tempHeight   = containerHeight;

                        offsetTop    = 0;
                        offsetLeft   = (containerWidth - tempWidth) / 2;
                    }
                } else if (imageWidth <= containerWidth && imageHeight >= containerHeight) {
                    // 图片宽度小于容器 高度大于容器  
                    tempHeight = containerHeight;
                    tempWidth  = imageRatio * containerHeight;

                    offsetLeft = (containerWidth - tempWidth) / 2;
                    offsetTop  = 0;
                } else if (imageWidth >= containerWidth && imageHeight <= containerHeight) {
                    // 图片宽度大于容器 图片高度小于容器
                    tempWidth  = containerWidth;
                    tempHeight = tempWidth / imageWidth * imageHeight;

                    offsetTop  = (containerHeight - tempHeight) / 2;
                    offsetLeft = 0;
                };          

                $("#bigPic").css('left', offsetLeft);
                $("#bigPic").css('top', offsetTop);
                $("#bigPic").width(tempWidth);
                $("#bigPic").height(tempHeight);   
                setTimeout(function() {
                    $(".comment-content .border-bottom").removeClass("border-bot");
                    $(".border-bottom").eq($(".comment-content").find(".border-bottom").length - 1).addClass("border-bot");
                }, 200)


            },
            construct: function() {

                this.listenTo(this.model, 'change', this.render);
                
            },
        });
    });
