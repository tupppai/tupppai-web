define([
        'marionette',
        'fancybox',  
        'tpl!app/templates/FooterView.html',
        'app/views/UploadingView'
     ],
    function (Marionette, fancybox, template, UploadingView) {
        "use strict";

        var footerView = Marionette.ItemView.extend({
            tagName: 'div',
            className: '',
            template : template,
            onRender: function() {
                var view = new UploadingView();
            }
        });

        return footerView;
    });
