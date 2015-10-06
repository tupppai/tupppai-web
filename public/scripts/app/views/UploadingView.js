define(['app/views/Base', 'tpl!app/templates/UploadingView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this;
                $(".uploading-popup").fancybox({});
            }
        });
    });
