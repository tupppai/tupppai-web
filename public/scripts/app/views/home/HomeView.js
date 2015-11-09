define(['marionette', 'app/views/Base'],
    function (Marionette, View) {
        "use strict";

        var homeListView = '#homeListView';

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            construct: function () {
                var self = this;
                $(homeListView).empty();
                self.listenTo(self.collection, 'change', self.render);
                self.scroll();

                self.collection.data.uid = $(window.app.home.el).attr('data-uid');
                self.collection.data.page = 0;
                self.collection.loading();
            },
            render: function() {

                var template = this.template;
                var el       = $(homeListView);
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
                this.onRender();
            }
        });
    });
