define(['app/views/Base', 'app/collections/Asks', 'tpl!app/templates/index/IndexItemView.html'],
    function (View, Asks,  template) {
        "use strict";

        var indexItemView = '#indexItemView';
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collection: Asks,
            construct: function() { 
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
                self.collection.loading(self.showEmptyView);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#contentView"), ".emptyContentView");
                }
            },
            render: function() {
                var template = this.template;

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    append(indexItemView, html);
                });
                this.onRender();
            }   
        });
    });
