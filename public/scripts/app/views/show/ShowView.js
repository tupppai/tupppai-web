define(['app/views/Base', 'app/models/Base','app/models/Ask', 'tpl!app/templates/show/ShowView.html'],
	function(View, ModelBase, Ask, template) {
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
                var value = 1;
                if( $(e.currentTarget).hasClass('icon-like-large-pressed') ){
                    value = -1;
                }

                $(e.currentTarget).toggleClass('icon-like-large-pressed');
                $(e.currentTarget).siblings('.replyItem-actionbar-like-count').toggleClass('icon-like-color');

                var likeEle = $(e.currentTarget).siblings('.replyItem-actionbar-like-count');
                var linkCount = likeEle.text( Number(likeEle.text())+value );
            },
            askLikeToggle: function(e) {
                var value = 1;
                if( $(e.currentTarget).hasClass('icon-like-pressed') ){
                    value = -1;
                }

                $(e.currentTarget).toggleClass('icon-like-pressed');
                $(e.currentTarget).siblings('.actionbar-like-count').toggleClass('icon-like-color');

                var likeEle = $(e.currentTarget).siblings('.actionbar-like-count');
                var linkCount = likeEle.text( Number(likeEle.text())+value );
            },
			construct: function() {
				var self = this;
				window.app.content.close();
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
