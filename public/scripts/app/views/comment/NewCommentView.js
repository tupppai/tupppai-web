define(['app/views/Base', 'tpl!app/templates/comment/NewCommentView.html'],
    function (View, template) {
        "use strict";
       
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
			construct: function() {
				var self = this;
				this.listenTo(this.collection, 'change', this.render);

                self.scroll();
                self.collection.loadMore();
            },
            render: function() {
                console.log(this.collection);
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
