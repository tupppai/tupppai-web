define(['underscore', 'app/views/Base', 'app/models/Base', 'tpl!app/templates/PopupView.html'],
    function (_, View, ModelBase, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                this.listenTo(this.model, 'change', this.render);
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
            }
        });
    });
