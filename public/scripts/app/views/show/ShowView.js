define(['app/views/Base', 'app/models/Base','app/models/Ask', 'app/models/Like', 'tpl!app/templates/show/ShowView.html'],
	function(View, ModelBase, Ask, Like, template) {
		"use strict"

		return View.extend({
			tagName: 'div',
			className: '',
			template: template,
            events: {
                'click .icon-like-large' : 'replyLikeToggle',
                'click .download': 'downloadClick',
                'click .ask-like-icon': 'askLikeToggle',
                'click .ask-origin-content img' : 'askImagePopup',
                'click .reply-item-content img' : 'showImagePopup',
                "click .photo-item-reply" : "photoShift",
            },
             photoShift: function(e) {
                     var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                     var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                     $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                     $(e.currentTarget).find('img').attr("src",AskLargerUrl);              
            },
            showImagePopup:function(e) {
                var askSrc = $('.ask-origin-content img').attr('src');
                var showSrc = $(e.currentTarget).attr('src');
                $('#ask_picture').attr('src',askSrc); 
                $('#show_picture').attr('src',showSrc); 
                $('.picture-product').removeClass('hide');
                
            },
            askImagePopup: function(e) {
                var askSrc = $(e.currentTarget).attr('src');
                $('#ask_picture').attr('src',askSrc); 
                $('.picture-product').addClass('hide');
            },
            replyLikeToggle: function(e) {
                var value = 1;
                if( $(e.currentTarget).hasClass('icon-like-large-pressed') ){
                    value = -1;
                }

                var id = $(e.target).attr('data-id');
                var like = new Like({
                    id: id,
                    type: 2,
                    status: value 
                });

                like.save(function(){

                    $(e.currentTarget).toggleClass('icon-like-large-pressed');
                    $(e.currentTarget).siblings('.replyItem-actionbar-like-count').toggleClass('icon-like-color');

                    var likeEle = $(e.currentTarget).siblings('.replyItem-actionbar-like-count');
                    var linkCount = likeEle.text( Number(likeEle.text())+value );
                });
            },
            askLikeToggle: function(e) {
                var value = 1;
                if( $(e.currentTarget).hasClass('icon-like-pressed') ){
                    value = -1;
                }

                var id = $(e.target).attr('data-id');
                var like = new Like({
                    id: id,
                    type: 1,
                    status: value 
                });

                like.save(function(){
                    $(e.currentTarget).toggleClass('icon-like-pressed');
                    $(e.currentTarget).siblings('.actionbar-like-count').toggleClass('icon-like-color');

                    var likeEle = $(e.currentTarget).siblings('.actionbar-like-count');
                    var linkCount = likeEle.text( Number(likeEle.text())+value );
                });
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
