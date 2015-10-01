define(['app/views/Base', 'app/models/Base', 'tpl!app/templates/ShowView.html'],
	function(View, ModelBase, template) {
		"use strict"

		return View.extend({
			tagName: 'div',
			className: '',
			template: template,
            events: {
                'click .icon-like-large' : 'like_toggle',
                'click .download': 'downloadClick',
                'click .ask-like-icon': 'askLikeToggle',
            },
            like_toggle: function(e) {
                $(e.currentTarget).toggleClass('icon-like-large-pressed');
            },
            askLikeToggle: function(e) {
                $(e.currentTarget).toggleClass('icon-like-pressed');
            },
			construct: function() {
				var self = this;
				window.app.content.close();
				this.listenTo(this.model, 'change', this.render);
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
