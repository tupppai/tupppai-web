define(['marionette', 'app/views/Base'],
    function (Marionette, View) {
        "use strict";

        var homeListView = '#homeListView';

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            data: 0,
            construct: function () {
                var self = this;
                $(homeListView).empty();
                self.scroll();

                self.collection.data.uid = $(window.app.home.el).attr('uid');
                self.collection.data.page = 0;
                self.collection.loadMore(function(){ 
                    //todo: 优化成单条添加
                    self.render();
                    //self.collection.trigger('change');
                });

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
