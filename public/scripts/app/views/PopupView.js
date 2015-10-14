define(['underscore', 'app/views/Base', 'app/models/Base', 'tpl!app/templates/PopupView.html', 'app/views/PopupView'],
    function (_, View, ModelBase, template, PopupView) {
        "use strict";
        
        return View.extend({
            template: template,
			events: {
                'click .download': 'downloadClick',
			},
            construct: function () {
                var self = this;

                this.listenTo(this.model, 'change', this.render);
                $(".fancybox").fancybox({
                    afterShow: function(){
                        $(".download").click(self.downloadClick);
                    }
                });
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
