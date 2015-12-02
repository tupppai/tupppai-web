define(['app/views/Base', 'tpl!app/templates/comment/NewCommentView.html', 'emojione'],
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
                self.collection.loading();
            },
            render: function() {
                var template = this.template;
                var el       = $(this.el);
                this.collection.each(function(model) {
                    model.set('content', emojione.unicodeToImage(model.toJSON().content));

                    var html = template(model.toJSON());
                    el.append(html);
                });
                this.onRender();
            }
        });
    });
