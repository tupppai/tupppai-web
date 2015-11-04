define(['app/views/Base',   'tpl!app/templates/comment/CommentItemView.html'],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .photo-item-reply" : "photoShift"
            },
            // 求助图片切换
            photoShift: function(e) {
                     var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                     var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                     $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                     $(e.currentTarget).find('img').attr("src",AskLargerUrl);              
            },
            construct: function() {
				var self = this;
				this.listenTo(this.model, 'change', this.render);
			},
        });
    });
