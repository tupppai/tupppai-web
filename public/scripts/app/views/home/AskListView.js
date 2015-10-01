define(['app/views/home/ListView', 'app/models/Base', 'app/collections/Asks', 'tpl!app/templates/home/AskItemView.html'],
    function (View, ModelBase, Asks, askItemTemplate) {
        "use strict";

        var asks = new Asks;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            data: 0,
            collection: asks,
            template: askItemTemplate,
            //events: {
            //    'click .download': 'downloadClick',
            //},
            onRender: function() {
                $(".download").click(this.downloadClick);
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
        });
    });
