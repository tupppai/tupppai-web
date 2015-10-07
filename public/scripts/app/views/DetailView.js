define([
        'app/views/Base', 
        'app/models/Base',
        'app/collections/Asks', 
        'app/models/Ask', 
        'tpl!app/templates/DetailView.html',
        'tpl!app/templates/CommentView.html'
        ],
    function (View, ModelBase, Asks, Ask, template, CommentView) {

		"use strict"

		return View.extend({
            collection: Asks,
			tagName: 'div',
			className: '',
			template: template,
            commentTemplates: CommentView,
			events: {
				'click .icon-like-large' : 'like_toggle',
                'click .comment-link-toggle' : 'commentLinkToggle',
                'click .reply-btn' : 'commentFrameToggle',
                'click .download': 'downloadClick',
                'click #comment-btn': 'commentContent',
			},
			construct: function() {
                this.listenTo(this.model, 'change', this.render);
                debugger;
                var html = this.commentTemplates;
                $('#newest-content').append(html);
			},
			like_toggle: function(e) {
				$(e.currentTarget).toggleClass('icon-like-large-pressed');
			},
			commentLinkToggle: function(e) {
                $(e.currentTarget).toggleClass('comment-link-icon-pressed');
            },
            commentFrameToggle: function(e) {
            	$(e.currentTarget).parent().parent().parent().next().toggleClass('hide');
            },
            commentContent: function(e) {
                debugger;
                var content = $(e.currentTarget).prev().val();
                $('#newest-content').append(content);
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
            rander: function() {
                alert(123);
            }
            
		})
	})
