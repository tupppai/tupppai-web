define([
        'app/views/Base', 
        'tpl!app/templates/replydetailplay/ReplyDetailCommentView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .inp-reply" : "replyComment",
                "click .reply-cancel" : "replyNone",
            },
            construct: function() {
                var self = this;
                this.listenTo(this.collection, 'change', this.render);
                self.collection.loading();
            },
            replyNone: function(e) {
                $(".inp-frame").addClass("blo");
            },
            replyComment: function(e) {
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
    
        });
    });
