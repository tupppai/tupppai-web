define(['app/views/Base', 'tpl!app/templates/LoginView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this;
            },
            render: function() {
                var html = this.template();

                $("#modalView").append(html);
                $('div[data-remodal-id=login-modal]').remodal().open();
            }
        });
    });
