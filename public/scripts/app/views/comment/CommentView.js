define(['app/views/Base', 'app/models/Base', 'app/models/Ask', 'tpl!app/templates/comment/CommentView.html','app/collections/Comments'],
	function( View, ModelBase, Ask, template, Comments) {
		"use strict"

		return View.extend({
			tagName: 'div',
			className: '',
			template: template,
			events: {
				'click .icon-like-large' : 'like_toggle',
                'click .comment-link-toggle' : 'commentLinkToggle',
                'click .reply-btn' : 'commentFrameToggle',
                'click .download': 'downloadClick',
			},
			construct: function() {
			},
			like_toggle: function(e) {
				$(e.currentTarget).toggleClass('icon-like-large-pressed');
			},
			commentLinkToggle: function(e) {
                var value = 1;
                if( $(e.currentTarget).hasClass('comment-link-icon-pressed') ){
                    value = -1;
                }
                $(e.currentTarget).toggleClass('comment-link-icon-pressed');
                $(e.currentTarget).siblings('.actionbar-like-count').toggleClass('icon-like-color');

                var likeEle = $(e.currentTarget).siblings('.actionbar-like-count');
                var linkCount = likeEle.text( Number(likeEle.text())+value );

            },
            commentFrameToggle: function(e) {
            	$(e.currentTarget).parent().parent().parent().next().toggleClass('hide');
            },
			downloadClick: function(e) {
                var data = $(e.currentTarget).attr("data");
                var id   = $(e.currentTarget).attr("data-id");
                var model = new ModelBase;
                model.url = '/record?type='+data+'&target='+id;
                model.fetch({
                    success: function(data) {
                        var urls = data.get('url');
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                        });
                    }
                });
            },
            
		})
	})
