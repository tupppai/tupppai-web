define(['tpl!app/views/detail/detailWorks/detailWorks.html'],
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
                "click .follow" : "follow",
            },
            onShow: function() {
                $("#footer-section").css({
                    paddingBottom: "3rem"
                })
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
                var id = $(e.currentTarget).attr('dataId');
                var type = $(e.currentTarget).attr('dataType');
                var inset = $(e.currentTarget).attr('inset')
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
                    var comment_id = data.comment_id,
                        reply_to =  data.reply_to,
                        target_id = data.target_id,
                        type = data.data_type,
                        user_name = data.user_name,
                        content = data.content;

                    var comment ='<div data-type='+ "\"" + type + "\""+'target-id='+ "\"" + target_id + "\""+'reply-to='+ "\"" + reply_to + "\""+'comment-id='+ "\"" + comment_id + "\"" +' class="commentDetail"><div class="commentLine"><div class="commentHead clearfix"><span class="userName userName-reply">'+ user_name +'：</span><span class="commentText">'+ content +'</span></div></div></div>'
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
                var el = $(e.currentTarget);
                var inset = el.attr('inset')
                var content = el.parents("#replyWindow").find(".windowContent").val();
                var reply_to = el.attr('reply-to');
                var type = el.attr('data-type');
                var comment_id = el.attr('comment-id');
                var target_id = el.attr('target-id');
                var postData = {
                    'content': content,
                    'type' : type,
                    'id': target_id,
                    'reply_to' : reply_to,
                    'for_comment' : comment_id
                };
                $.post('/v2/comments/save', postData, function( data ){
                    var comment_id = data.comment_id,
                        reply_to =  data.reply_to,
                        target_id = data.target_id,
                        type = data.data_type,
                        user_name = data.user_name,
                        content = data.content,
                        reply_name = data.reply_name;

                    var comment ='<div data-type='+ "\"" + type + "\""+'target-id='+ "\"" + target_id + "\""+'reply-to='+ "\"" + reply_to + "\""+'comment-id='+ "\"" + comment_id + "\"" +' class="commentDetail"><div class="commentLine commentReply"><div class="commentHead clearfix"><span class="userNameGroup"><span class="userName-reply">'+ user_name +'</span><em>回复</em><span class="userName-beReplied">'+ reply_name +':</span></span><span class="commentText">'+ content +'</span></div></div></div>'
                    $("#" + inset).after(comment); //把新增评论插入页面
                    $("#" + inset).siblings(".commentDetail").eq(3).remove(); //移除第三条评论
                    $(".windowContent").val(""); //清空评论框
                    $("#replyWindow").addClass("hide") //隐藏评论弹窗
                    var title = '回复成功';
                    fntoast(title);
                });
            },
            download: function(e) {
                var id = $(e.currentTarget).attr("data-id");
                $.get('/record?type=1&target='+ id, function(data) {
                    var title = '添加成功，在"进行中"等你下载喽';
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
                $(".replyTo").text(name)
                var targetId = $(e.currentTarget).attr("target-id");
                var commentId = $(e.currentTarget).attr("comment-id");
                var replyTo = $(e.currentTarget).attr("reply-to");
                var dataType = $(e.currentTarget).attr("data-type");
                var inset = $(e.currentTarget).siblings(".sectionFooter").attr("id"); //该插到什么地方

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
                var inset = $(e.currentTarget).parents(".sectionFooter").attr("id"); //该插到什么地方

                $("#replySend").attr("dataId", dataId);
                $("#replySend").attr("dataType", dataType);
                $("#replySend").attr("inset", inset);
            },
            replyPopupHide: function(e) {
                $(".window-fix").addClass("hide");
            },            
            windowFix: function(e) {
                if($(e.target).hasClass("window-fix")) {
                    $(e.currentTarget).addClass("hide");
                }
            },
        });
    });


