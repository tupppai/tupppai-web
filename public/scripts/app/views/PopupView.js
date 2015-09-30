define(['app/views/Base', 'app/models/Base', 'tpl!app/templates/PopupView.html'],
    function (View, ModelBase, template) {
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
                model.url = '/download';
                model.data= {
                    type: data,
                    target: id
                }
                model.fetch({
                    success: function(data) {
                    }
                });
                
                console.log(e);
            }
        });
    });
