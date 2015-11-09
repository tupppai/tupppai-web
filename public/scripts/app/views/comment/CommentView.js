define([
        'app/views/Base', 
        'app/models/Base', 
        'app/models/Ask', 
        'app/models/Like', 
        'tpl!app/templates/comment/CommentView.html',
        'app/collections/Comments'
       ],
	function( View, ModelBase, Ask, Like, template, Comments) {
		"use strict"

		return View.extend({
			tagName: 'div',
			className: '',
			template: template,
			events: {
				'click .like_toggle' : 'likeToggle',
                'click .comment-link-toggle' : 'commentLinkToggle',
                'click .reply-btn' : 'commentFrameToggle',
                'click .download': 'download',
                'click #comment-btn': 'commentReply',
                'click .ask-item-picture img' : 'askImagePopup',
                "click .photo-item-reply" : "photoShift"
			},
            photoShift: function(e) {
                 var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                 var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                 $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                 $(e.currentTarget).find('img').attr("src",AskLargerUrl);              
            },
            askImagePopup: function(e) {
                var askSrc = $(e.currentTarget).attr('src');
                $('#ask_picture').attr('src',askSrc);
                $('.picture-product').addClass('hide');
                $('.picture-original').css('width','100%');
            },
            commentFrameToggle: function(e) {
            	$(e.currentTarget).parent().parent().parent().next().toggleClass('hide');
            },
            // 评论功能
            commentReply: function(e) {
                var id = $(window.app.content.el).attr('data-id');
                var type = $(window.app.content.el).attr('data-type');
                var content = $(e.currentTarget).prev().val();
                $.post('/comments/save', {
                    id: id,
                    type: type,
                    content: content
                }, function(data) {
                    if(data.ret == 1){
                        //todo: upgrade append
                        location.reload();
                        var t = $(document);
                        t.scrollTop(t.height());
                    }
                    else {
                        $(".login-popup").click();
                    }
                });
               //location.reload();
            }            
		});
	});
