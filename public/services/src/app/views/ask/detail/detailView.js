define(['tpl!app/views/ask/detail/detail.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'detail-padding',
            template: template,
            events: {
            	"click .commentDetail": "replyPopup",
            	"click .cancel": "replyPopupHide",
                "click .window-fix": "windowFix",
                "click .comment-btn": "commons",
            	"click .like-btn": "clickLike",
                "click .share": 'clickShare',
                "click .share-mask" : "clickShare",
                "click .footerHelp" : "download",
                "click #replySend" : "worksComment",
                "click #replyComment" : "replyComment",
                "click #replyComment" : "oldComment",
            },
            // 分享朋友
            clickShare: function(e) {
                $(".share-mask").removeClass("hide");
                if($(e.target).hasClass("share-mask")) {
                    $(".share-mask").addClass("hide");
                };
            },
            //评论作品
            worksComment: function(e) {
                var id = $(e.currentTarget).attr('dataId');
                var type = $(e.currentTarget).attr('dataType');
                var content = $("#commentWindow .windowContent").val();
                if(!content || content == "") {
                    toast("内容不能为空");
                    return false;
                }
                $.post('/v2/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    debugger;
                    $("#commentWindow").addClass("hide");

                });
            },
            //回复评论
            replyComment: function(e) {
                var el = $(e.currentTarget);
                var content = el.parents("#replyWindow").find(".windowContent").val();
                var reply_to = el.attr('reply-to');
                var type = el.attr('data-type');
                var comment_id = el.attr('comment-id');
                var target_id = el.attr('target-id');
                var url = "/v2/comments/save";
                var postData = {
                    'content': content,
                    'type' : type,
                    'id': target_id,
                    'reply_to' : reply_to,
                    'for_comment' : comment_id
                };
                $.post(url, postData, function( returnData ){
                    $("#replyWindow").addClass("hide")
                });
            },
            //原图评论
            oldComment: function(e) {
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
            download: function(e) {
                var type = $(e.currentTarget).attr("data-type");
                var id   = $(e.currentTarget).attr("data-id");
                var category_id = $(e.currentTarget).attr("category-id");
                
                if( category_id == 'undefine' ) {
                    var category_id = 0;
                }

                $.get('/record?type='+ type +'&target='+ id +'&category_id='+ category_id, function(data) {
                    parse(data);
                    console.log(data)
                    if(data.ret == 1) {
                        var data = data.data;
                        var urls = data.url;
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                            console.log(location.href)
                        });
                        toast('已下载该图片，到进行中处理');
                    }
                });
            },

            clickLike: function(e) {
                var loveCount = $(e.currentTarget).attr('love-count');
                var id   = $(e.currentTarget).attr('data-id');
                var likeEle = $(e.currentTarget).find('.text-like-btn');
                var type   = 2;
                $.get('/v2/love', {
                    id: id,
                    num: loveCount,
                    type: 2
                }, function(data) {
                    $(e.currentTarget).addClass("liked-icon")
                    likeEle.text( Number(likeEle.text()) + 1 );
                })
            },
            replyPopup: function(e) {
            	$("#replyWindow").removeClass("hide");
                var name = $(e.currentTarget).find(".userName-reply").text();
                $(".replyTo").text(name)
                var targetId = $(e.currentTarget).attr("target-id");
                var commentId = $(e.currentTarget).attr("comment-id");
                var replyTo = $(e.currentTarget).attr("reply-to");
                var dataType = $(e.currentTarget).attr("data-type");

                $("#replyComment").attr("target-id", targetId);
                $("#replyComment").attr("comment-id", commentId);
                $("#replyComment").attr("reply-to", replyTo);
                $("#replyComment").attr("data-type", dataType);
            },            
            replyPopupHide: function(e) {
            	$(".window-fix").addClass("hide");
            },            
            windowFix: function(e) {
            	if($(e.target).hasClass("window-fix")) {
            		$(e.currentTarget).addClass("hide");
            	}
            },
            commons: function(e) {
                $("#commentWindow").removeClass("hide");
                var dataId = $(e.currentTarget).attr('data-id');
                var dataType = $(e.currentTarget).attr('data-type');
                $("#replySend").attr("dataId", dataId);
                $("#replySend").attr("dataType", dataType);
            }
        });
    });


