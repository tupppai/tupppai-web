define([
        'underscore',
        'app/views/Base',
        'app/collections/Inprogresses',
        'tpl!app/templates/upload/InprogressItemView.html'
       ],
    function (_, View, Inprogresses, template) {

        var InprogressItemView = '#InprogressItemView';

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collection: Inprogresses,

            construct: function() { 
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
                self.collection.loading(self.showEmptyView);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#InprogressItemView"), ".emptyContentView");
                }
            },
            render: function() {
                var template = this.template;

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    append(InprogressItemView, html);
                });
                this.onRender();
            },
        });
    });
