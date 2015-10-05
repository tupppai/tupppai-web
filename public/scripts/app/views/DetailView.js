define(['app/views/Base', 'app/models/Base', 'app/models/Ask', 'tpl!app/templates/DetailView.html'],
	function( View, ModelBase, Ask, template) {
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
                this.listenTo(this.model, 'change', this.render);
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
            }
		})
	})
