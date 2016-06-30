define(['tpl!app/views/detail/detailContent/detailContent.html', 'fx', 'pingpp'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .commentDetail": "replyPopup",
            	"click .cancel": "replyPopupHide",
                "click .window-fix": "windowFix",
                "click .reward-toast": "windowFix",
                "click .comment-btn": "commons",
            	"click .like-btn": "clickLike",
                "click .share": 'clickShare',
                "click .share-mask" : "clickShare",
                "click .footerHelp" : "download",
                "click #replySend" : "worksComment",
                "click #replyComment" : "replyComment",
                "click .follow" : "follow",
                "click .original-img" : "originalBig",
                "click .reward" : "rewardShow",
                "click #rewardratuity" : "rewardGratuity",
            },
            onShow: function() {
                this.imageLazyLoad();
            },
            //打赏
            rewardShow: function(e) {
                $(".reward-toast").removeClass("hide");
            },
            rewardGratuity: function(e) {
                var money = $(".reward-money").val() || $(".reward-money").attr("placeholder"),
                    message = $(".message").val() || $(".message").attr("placeholder"),
                    target_id = $(".sectionContent").attr("target-id"),
                    type = $(".sectionContent").attr("data-type");
                    if(money < 0.01) {
                        fntoast("金额不能小于0.01元！")
                    } else {
                        $.post('/v2/account/recharge',{amount: money},
                            function(charge) {
                                pingpp.createPayment(charge, function(result, err){
                                    if( result == 'success') {
                                        $.post(
                                            '/v2/thread/reward',
                                            {
                                                amount: money,
                                                comment: message,
                                                target_id: target_id,
                                                target_type: type
                                            },
                                            function(data) {
                                                fntoast("支付成功！")
                                                setTimeout(function() {
                                                    window.location.reload(); 
                                                }, 2000)
                                            }

                                        )
                                    }   else {
                                        fntoast("请支付！")
                                    }
                                })
                            }
                        )
                    }

            },
            //点击原图变大
            originalBig: function(e) {
                var dataTop,
                    dataLeft,
                    dataWidth,
                    dataHeight,
                    imageRatio;
                if(!$(e.currentTarget).hasClass("channge-big")) {
                        dataTop = parseInt($(e.currentTarget).css("top"));
                        dataLeft = parseInt($(e.currentTarget).css("left"));
                        dataWidth = $(e.currentTarget).width();
                        dataHeight = $(e.currentTarget).height();
                        imageRatio = $(e.currentTarget).attr("imageRatio");

                        $(e.currentTarget).attr("dataTop", dataTop)
                        $(e.currentTarget).attr("dataLeft", dataLeft)
                        $(e.currentTarget).attr("dataWidth", dataWidth)
                        $(e.currentTarget).attr("dataHeight", dataHeight);

                    $(e.currentTarget).addClass("channge-big").animate({
                        top: 0,
                        left: 0,
                        width: "24.8rem",
                        height: 24.8 * imageRatio + "rem",
                        zIndex: 20
                    }, 100).parent().addClass("original-pic-auto");
                } else {
                    dataTop = $(e.currentTarget).attr("dataTop")
                    dataLeft = $(e.currentTarget).attr("dataLeft")
                    dataWidth = $(e.currentTarget).attr("dataWidth")
                    dataHeight = $(e.currentTarget).attr("dataHeight");
                    $(e.currentTarget).removeClass("channge-big").animate({
                        top: dataTop + "px",
                        left: dataLeft + "px",
                        width: dataWidth + "px",
                        height: dataHeight + "px",
                    }, 100, function() {
                        $(e.currentTarget).css({zIndex: 0}).parent().removeClass("original-pic-auto")
                    });
                }
            },
            //关注
            follow: function(e) {
                var dataUid = $(e.currentTarget).attr("data-uid");
                $.post('/user/follow', {
                    uid: dataUid,
                    status: 1
                }, function(data) {
                    $(".follow[remove=follow" + dataUid + "]").addClass("hide");
                    fntoast("关注成功");
                });
            },
            // 分享朋友
            clickShare: function(e) {
                $(".share-mask").removeClass("hide");
                if($(e.target).hasClass("share-mask")) {
                    $(".share-mask").addClass("hide");
                };
            },
            //发布评论
            worksComment: function(e) {
                var id = $(e.currentTarget).attr('dataId'),
                    type = $(e.currentTarget).attr('dataType'),
                    inset = $(e.currentTarget).attr('inset'), //把新增评论插入页面
                    content = $("#commentWindow .windowContent").val(),
                    src = $(".personalCenter").find("img").attr("src"); //获取评论人头像
                if(!content || content == "") {
                    toast("内容不能为空");
                    return false;
                };
                $.post('/v2/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    var comment_id = data.comment_id,
                        reply_to =  data.reply_to,
                        target_id = data.target_id,
                        user_name = data.user_name;

                    var comment ='<div data-type='+ "\"" + type + "\""+'target-id='+ "\"" + target_id + "\""+'reply-to='+ "\"" + reply_to + "\""+'comment-id='+ "\"" + comment_id + "\"" +' class="commentDetail"><div class="comment-list clearfix"><a href="#personal/index/' + data.reply_to + '" class="comment-avatar"><img src="' + src + '" alt=""></a><div class="commentHead clearfix"><span class="userName userName-reply">'+ user_name +'：</span></div><span class="commentText">'+ content +'</span></div></div>'
                    $("#" + inset).after(comment);  //把新增评论插入页面
                    $("#" + inset).siblings(".commentDetail").eq(3).remove();   //移除最后一条评论
                    $(".windowContent").val("");    //清空评论框
                    $("#commentWindow").addClass("hide");   //隐藏评论弹窗
                    $(".rob-sofa").addClass("hide");   //隐藏无评论状态
                    var title = '评论成功';
                    fntoast(title);
                });
            },
            //回复评论
            replyComment: function(e) {
                var el = $(e.currentTarget),
                    inset = el.attr('inset'),
                    content = el.parents("#replyWindow").find(".windowContent").val(),
                    reply_to = el.attr('reply-to'),
                    type = el.attr('data-type'),
                    comment_id = el.attr('comment-id'),
                    target_id = el.attr('target-id'),
                    postData = {
                        'content': content,
                        'type' : type,
                        'id': target_id,
                        'reply_to' : reply_to,
                        'for_comment' : comment_id
                    },
                    src = $(".personalCenter").find("img").attr("src");
                $.post('/v2/comments/save', postData, function( data ){
                    var comment_id = data.comment_id,
                        reply_to =  data.reply_to,
                        target_id = data.target_id,
                        type = data.data_type,
                        user_name = data.user_name,
                        content = data.content,
                        reply_name = data.reply_name;
                        
                    var comment ='<div data-type='+ "\"" + type + "\""+'target-id='+ "\"" + target_id + "\""+'reply-to='+ "\"" + reply_to + "\""+'comment-id='+ "\"" + comment_id + "\"" +' class="commentDetail"><div class="comment-list clearfix"><a href="#personal/index/' + data.reply_to + '" class="comment-avatar"><img src="' + src + '" alt=""></a><div class="commentHead clearfix"><span class="userNameGroup"><span class="userName-reply">'+ user_name +'</span><em>回复</em><span class="userName-beReplied">'+ reply_name +':</span></span></div><span class="commentText">'+ content +'</span></div></div>'
                    $("#" + inset).after(comment); //把新增评论插入页面
                    $("#" + inset).siblings(".commentDetail").eq(3).remove(); //移除第三条评论
                    $(".windowContent").val(""); //清空评论框
                    $("#replyWindow").addClass("hide") //隐藏评论弹窗
                    var title = '回复成功';
                    fntoast(title);
                });
            },
            //点赞
            clickLike: function(e) {
                var loveCount = $(e.currentTarget).attr('love-count');
                var id   = $(e.currentTarget).attr('data-id');
                var likeEle = $(e.currentTarget).find('.text-like-btn');
                var type   = 2;
                if(!$(e.currentTarget).hasClass("liked-icon")) {
                    $.get('/v2/love', {
                        id: id,
                        num: loveCount,
                        type: 2
                    }, function(data) {
                        $(e.currentTarget).addClass("liked-icon")
                        likeEle.text( Number(likeEle.text()) + 1 );
                        var title = '点赞成功';
                        fntoast(title);
                    })
                }
            },
            //弹出回复评论框
            replyPopup: function(e) {
            	$("#replyWindow").removeClass("hide");
                var name = $(e.currentTarget).find(".userName-reply").text();
                $(".replyTo").text(name);
                var targetId = $(e.currentTarget).attr("target-id");
                var commentId = $(e.currentTarget).attr("comment-id");
                var replyTo = $(e.currentTarget).attr("reply-to");
                var dataType = $(e.currentTarget).attr("data-type");
                var inset = $(e.currentTarget).siblings("#insetPosition").attr("id"); //该插到什么地方

                $("#replyComment").attr("target-id", targetId);
                $("#replyComment").attr("comment-id", commentId);
                $("#replyComment").attr("reply-to", replyTo);
                $("#replyComment").attr("data-type", dataType);
                $("#replyComment").attr("inset", inset);
            },            
            //弹出评论框
            commons: function(e) {
                $("#commentWindow").removeClass("hide");
                var dataId = $(e.currentTarget).attr('data-id');
                var dataType = $(e.currentTarget).attr('data-type');
                var inset = $(e.currentTarget).parents(".sectionFooter").siblings("#insetPosition").attr("id"); //该插到什么地方

                $("#replySend").attr("dataId", dataId);
                $("#replySend").attr("dataType", dataType);
                $("#replySend").attr("inset", inset);
            },
            //点击取消关闭评论弹窗           
            replyPopupHide: function(e) {
                $(".window-fix").addClass("hide");
            }, 
            //关闭评论弹窗           
            windowFix: function(e) {
                if($(e.target).hasClass("window-fix") || $(e.target).hasClass("reward-toast")) {
                    $(e.currentTarget).addClass("hide");
                }
            },
            imageLazyLoad: function() {
                $("img.original-img").lazyload({
                    effect: "fadeIn",
                    threshold : 50,
                    load: function(image, count) {
                        //获取原始长宽
                        var image = image[0];
                        var imageWidth = image.naturalWidth;
                        var imageHeight = image.naturalHeight;
                        var imageRatio = imageWidth/imageHeight;
                        
                        var container = $(image).parent();
                        var containerWidth = $(container).width();
                        var containerHeight = $(container).height();
                        var tempWidth = 0;
                        var tempHeight = 0;
                        var offsetLeft = 0;
                        var offsetTop  = 0;
                        if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                            if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                                tempHeight = containerHeight;
                                tempWidth  = imageWidth * containerHeight / imageHeight;
                                offsetLeft = (containerWidth - tempWidth) / 2;
                                offsetTop  = 0;
            
                            } else {
                                tempWidth  = containerWidth;
                                tempHeight = imageHeight * containerWidth / imageWidth;
                                offsetLeft = 0;
                                offsetTop  = (containerHeight - tempHeight) / 2;
                            } 
                        } else if (imageWidth <= containerWidth && imageHeight <= containerHeight) {
                            if (imageRatio > containerWidth / containerHeight) {
                                tempHeight   = containerHeight;
                                tempWidth    = imageWidth * containerHeight / imageHeight;
                                offsetTop    = 0;
                                offsetLeft   = (imageWidth - tempWidth) / 2;
                            } else {
                                tempWidth    = containerWidth;
                                tempHeight   = imageHeight * containerWidth / imageWidth
                                offsetLeft   = 0;
                                offsetTop    = (imageHeight - tempHeight) / 2;
                            }
                        } else if (imageWidth <= containerWidth && imageHeight > containerHeight) { 
                            tempWidth  = containerWidth;
                            tempHeight = imageHeight * containerWidth / imageWidth;
                            offsetTop  = (imageHeight - tempHeight) / 2;
                            offsetLeft = 0;
                        } else if (imageWidth > containerWidth && imageHeight <= containerHeight) {                                               
                            tempHeight = containerHeight;
                            tempWidth  = imageRatio * containerHeight;
                            offsetLeft = (imageWidth - tempWidth) / 2;
                            offsetTop  = 0;
                        } 
                        if(imageWidth/imageHeight == containerWidth/containerHeight) {
                            tempHeight = containerHeight;
                            tempWidth = containerWidth;
                            offsetLeft = 0;
                            offsetTop  = 0;
                        }
                        
                        $(image).css('left', offsetLeft);
                        $(image).css('top', offsetTop);
                        $(image).width(tempWidth);
                        $(image).height(tempHeight); 
                    }
                });                
                $("img.origin-pic").lazyload({
                    effect: "fadeIn",
                    load: function(image, count) {
                        //获取原始长宽
                        var image = image[0];
                        var imageWidth = image.naturalWidth;
                        var imageHeight = image.naturalHeight;
                        var imageRatio = imageWidth/imageHeight;
                        
                        var container = $(image).parent();
                        var containerWidth = $(container).width();
                        var containerHeight = $(container).height();
                        var tempWidth = 0;
                        var tempHeight = 0;
                        var offsetLeft = 0;
                        var offsetTop  = 0;
                        if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                            if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                                tempHeight = containerHeight;
                                tempWidth  = imageWidth * containerHeight / imageHeight;
                                offsetLeft = (containerWidth - tempWidth) / 2;
                                offsetTop  = 0;
            
                            } else {
                                tempWidth  = containerWidth;
                                tempHeight = imageHeight * containerWidth / imageWidth;
                                offsetLeft = 0;
                                offsetTop  = (containerHeight - tempHeight) / 2;
                            } 
                        } else if (imageWidth <= containerWidth && imageHeight <= containerHeight) {
                            if (imageRatio > containerWidth / containerHeight) {
                                tempHeight   = containerHeight;
                                tempWidth    = imageWidth * containerHeight / imageHeight;
                                offsetTop    = 0;
                                offsetLeft   = (imageWidth - tempWidth) / 2;
                            } else {
                                tempWidth    = containerWidth;
                                tempHeight   = imageHeight * containerWidth / imageWidth
                                offsetLeft   = 0;
                                offsetTop    = (imageHeight - tempHeight) / 2;
                            }
                        } else if (imageWidth <= containerWidth && imageHeight > containerHeight) { 
                            tempWidth  = containerWidth;
                            tempHeight = imageHeight * containerWidth / imageWidth;
                            offsetTop  = (imageHeight - tempHeight) / 2;
                            offsetLeft = 0;
                        } else if (imageWidth > containerWidth && imageHeight <= containerHeight) {                                               
                            tempHeight = containerHeight;
                            tempWidth  = imageRatio * containerHeight;
                            offsetLeft = (imageWidth - tempWidth) / 2;
                            offsetTop  = 0;
                        } 
                        if(imageWidth/imageHeight == containerWidth/containerHeight) {
                            tempHeight = containerHeight;
                            tempWidth = containerWidth;
                            offsetLeft = 0;
                            offsetTop  = 0;
                        }
                        
                        $(image).css('left', offsetLeft);
                        $(image).css('top', offsetTop);
                        $(image).width(tempWidth);
                        $(image).height(tempHeight); 
                    }
                });
            },
        });
    });
