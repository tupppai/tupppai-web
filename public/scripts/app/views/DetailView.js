define(['app/views/Base','tpl!app/templates/DetailView.html'],
	function(View, template) {
		"use strict"

		return View.extend({
			tagName: 'div',
			className: '',
			template: template,
			events: {
				"click .icon-like-large" : "like_toggle",
                "click .comment-link-toggle" : "commentLinkToggle",
                "click .reply-btn" : "commentFrameToggle"
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
			construct: function() {
				var self = this;
				window.app.content.close();
				this.listenTo(this.model, 'change', this.render);
			}

		})
	})