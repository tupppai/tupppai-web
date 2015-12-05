define([
        'app/views/Base', 
        'app/models/Base',
        'app/models/Ask', 
        'app/models/Reply', 
        'app/models/Like',
        'app/models/AskReplies',
        'app/collections/Comments',
        'tpl!app/templates/replydetail/ReplyDetailView.html',
        'app/views/replydetail/ReplyCommentView',
        'app/views/replydetail/ReplyPersonView',
        'app/views/replydetail/ReplyActionBarView',
        'app/views/replydetail/ReplyImageView',
        'app/views/replydetail/ReplyBarView',
        'app/views/replydetail/ReplyCommentCountView',
       ],
    function (View, ModelBase, Ask, Reply, Like, AskReplies, Comments, template, ReplyCommentView, ReplyPersonView, ReplyActionBarView, ReplyImageView, ReplyBarView, ReplyCommentCountView) {
        "use strict"
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .written-reply": 'answer',
                "click #reply-left" : 'replyChange',
                "click #reply-right" : 'replyChange',
                "click .other-pic span" : 'replyChange',
                "click .like_toggle" : 'likeToggle',
                "click .original-pic" : 'originalChange',
                "click .ask-person" : "askPerson",
                "click .reply-person" : "replyPerson",
                "click #comment_btn" : "sendComment",
                "click .download" : "download",
                "click .reply-submit" : "replyCancel",
                "keyup #commentContent" : 'praiseText',
            },
            praiseText: function() {
                if($("#commentContent").val().length > 100) {
                    alert("字数请在100字以内")
                }
            },

            replyCancel : function() {
                $(".reply-inp").css({
                    display: "none"
                })
                $(".written-reply").css({
                    display: "block"
                })
            },

            sendComment:function(e) {
                var id = $(e.currentTarget).attr('data-id');
                var type = $(e.currentTarget).attr('data-type');
                var content = $(e.currentTarget).prev().val();

                $.post('/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    if(data.ret == 1){
                        toast('评论成功', function() {
                            location.reload();
                            setTimeout(function(){
                                alert(1)
                                $('.reply-trigger[data-id=' + id + ']').trigger("click");
                            },3000);
                        });
                        //todo: upgrade append
                        var t = $(document);
                        t.scrollTop(t.height());  
                    }
                    else {
                        $(".login-popup").click();
                    }
                });
            },
            replyPerson:function(e) {
                var reply_id = $(e.currentTarget).attr('data-id');
                var type = 2;

                $("#comment_btn").attr("data-id",reply_id);
                $("#comment_btn").attr("data-type", type);

                var model = new Reply;
                model.url = '/replies/' + reply_id;
                model.fetch();

                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyPersonView = new Backbone.Marionette.Region({el:"#replyPersonView"});
                var view = new ReplyPersonView({
                    model: model
                });
                replyPersonView.show(view); 

                var replyCommentCountView = new Backbone.Marionette.Region({el:"#replyCommentCountView"});
                var view = new ReplyCommentCountView({
                    model: model
                });
                replyCommentCountView.show(view); 

                var replyActionBarView = new Backbone.Marionette.Region({el:"#barView"});
                var view = new ReplyActionBarView({
                    model: model
                });
                replyActionBarView.show(view);
                
                var replyImageView = new Backbone.Marionette.Region({el:"#replyImageView"});
                var view = new ReplyImageView({
                    model: model
                });
                replyImageView.show(view);

                var replyCommentView = new Backbone.Marionette.Region({el:"#replyCommentView"});
                var view = new ReplyCommentView({
                    collection: comments
                });
                replyCommentView.show(view);


            },
            askPerson:function(e) {
                var reply_id = $(e.currentTarget).attr('data-id');
                var type = 1;

                $("#comment_btn").attr("data-id",reply_id);
                $("#comment_btn").attr("data-type", type);

                var model = new Ask;
                model.url = '/asks/' + reply_id;
                model.fetch();

                var comments = new Comments;
                comments.url = '/comments?target_type=new';
                comments.data.type = type;
                comments.data.target_id = reply_id;

                var replyPersonView = new Backbone.Marionette.Region({el:"#replyPersonView"});
                var view = new ReplyPersonView({
                    model: model
                });
                replyPersonView.show(view);    

                var replyCommentCountView = new Backbone.Marionette.Region({el:"#replyCommentCountView"});
                var view = new ReplyCommentCountView({
                    model: model
                });
                replyCommentCountView.show(view); 
                
                var replyBarView = new Backbone.Marionette.Region({el:"#barView"});
                var view = new ReplyBarView({
                    model: model
                });
                replyBarView.show(view);

                var replyCommentView = new Backbone.Marionette.Region({el:"#replyCommentView"});
                var view = new ReplyCommentView({
                    collection: comments
                });
                replyCommentView.show(view);
            },

            originalChange: function(e) {
                var replyImg = $(".original span img");
                var replyLength = replyImg.length;
                var replyIndex = $(e.currentTarget).index() - 1;
                var replySrc = replyImg.eq(replyIndex).attr("src");
                console.log(replyLength)

                $(".main-pic").attr("src", replySrc);
                $(".main-pic-blur").attr("src", replySrc);
                replyImg.eq(replyIndex).parent("span").addClass("original-change").siblings("span").removeClass("original-change");
                $(".pic").removeClass("img-change");
                
                $(".reply-right, .reply-left").css({
                        display: "block"
                })
            },
            construct: function() { 
                this.listenTo(this.model, 'change', this.render);
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
                var replyImg = $(".other-pic img");  //获取img
                var replyLength = replyImg.length; //获取img长度
                var replyIndex = parseInt($(".other-pic").attr("otherNum")); //获取索引值
                var dataIdx = parseInt($(e.currentTarget).attr("data-idx"));    //  
                var replySrc = null;
                var picIndex = null;
                //左右按钮
                console.log(replyLength)
                if(e.currentTarget.id == "reply-right") {
                    replyIndex++;
                    if (replyIndex >= (replyLength - 1)) {
                        replyIndex = (replyLength - 1);
                    };
                    $(".other-pic").attr("otherNum", replyIndex);
                } 
                if(e.currentTarget.id == "reply-left") {
                    replyIndex--;
                    if (replyIndex <= 0) {
                        replyIndex = 0;
                    };
                    $(".other-pic").attr("otherNum", replyIndex);
                }



                // 点击作品
                if(e.currentTarget.className == "reply-trigger pic person reply-person") {                    
                    replyIndex = $(e.currentTarget).index();
                    $(".other-pic").attr("otherNum", replyIndex);
                }

                replySrc = replyImg.eq(replyIndex).attr("src"); //获取当前图片的src
                $(".main-pic").attr("src", replySrc);
                $(".main-pic-blur").attr("src", replySrc);

                // 设置边框
                replyImg.eq(replyIndex).parent("span").addClass("img-change").siblings("span").removeClass("img-change");
                $(".original-pic").removeClass("original-change");

                if (replyIndex == (replyLength - 1)) {
                    $(".reply-right").css({
                        display: "none"
                    })
                } else {
                    $(".reply-right").css({
                        display: "block"
                    })
                };
                 if (replyIndex == 0) {
                    $(".reply-left").css({
                        display: "none"
                    })
                } else {
                     $(".reply-left").css({
                        display: "block"
                    })
                };

                dataIdx = replyIndex + 1;
                if (parseInt($(".other-pic").css("marginLeft")) == 0)  {
                    picIndex = 2;
                };
                if (dataIdx > picIndex && dataIdx < replyLength && dataIdx >= 2) {
                    $(".other-pic").animate({
                        marginLeft: - 90 * (dataIdx - 2) + "px"
                    }, 400);
                    picIndex = dataIdx;
                }

                $(".detail-comment").attr("data-id", replyImg.eq(replyIndex).parent("span").attr("data-id"));
                $(".detail-comment").attr("data-type", replyImg.eq(replyIndex).parent("span").attr("data-type"));
            },
        });
    });
