define(['app/views/Base', 'tpl!app/templates/DownloadView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
                      initialize: function () {
                $('.header-back').addClass('hidder-animation');
                $('.header').addClass('hidder-animation');
            },

        });
    });
