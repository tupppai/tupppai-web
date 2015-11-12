define([
        'app/views/Base', 
        'tpl!app/templates/message/CommentItemView.html'
        ],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click #reply_comment' : 'replyComment',
                'click #comment-btn' : 'CommentBtn'
            },
            CommentBtn:function(e) {
                var el = $(e.currentTarget).siblings('#commentContent');
                
                var content = el.val();
                var sender = el.attr('data-sender');
                var type = el.attr('type');
                var comment_id = el.attr('comment-id');
                var target_id = el.attr('target-id');

                var url = "/comments/save";

                var postData = {
                    'content': content,
                    'type' : type,
                    'id': target_id,
                    'reply_to' : sender,
                    'for_comment' : comment_id
                };
                $.post(url, postData, function( returnData ){
                    var info = returnData.info;
                    alert( info );
              
                    if( returnData.ret == 1 ) {
                        console.log(returnData.ret);
                        window.location.reload()
                    } 
                });
            },
            replyComment:function(e) {
                $(e.currentTarget).parents('.call-back').siblings('.comment-frame').toggleClass('hide');
                var has = $(e.currentTarget).parents('.call-back').siblings('.comment-frame').hasClass('hide');
                if(has) {
                    $(e.currentTarget).text('回复');
                } else {
                    $(e.currentTarget).text('收起');
                }
            },
            construct: function() {
                var self = this;
                $("a.menu-bar-item").removeClass('active');

                this.listenTo(this.collection, "change", this.render);

                self.scroll();
                self.collection.loading(self.showEmptyView);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#message-item-list"), ".emptyContentView");
                }
            }
        });
    });
