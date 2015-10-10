define(['app/views/Base', 'app/collections/Comments',  'tpl!app/templates/comment/CommentItemView.html'],
    function (View, Comment, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() {
				var self = this;
				this.listenTo(this.model, 'change', this.render);
			},
        });
    });
