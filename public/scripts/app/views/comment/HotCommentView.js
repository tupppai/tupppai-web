define(['app/views/Base', 'app/collections/Comments', 'tpl!app/templates/comment/HotCommentView.html'],
    function (View, Comment, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
			construct: function() {
				var self = this;
				// this.listenTo(this.collection, 'change', this.render);
			},
            render: function() {
                var template = this.template;
                var el       = $(this.el);
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
                this.onRender();

            }
        });
    });
