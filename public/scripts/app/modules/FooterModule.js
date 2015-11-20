define([
        'marionette',
        'fancybox',  
        'tpl!app/templates/FooterView.html',
        'app/views/upload/UploadingAskView',
        'app/views/upload/UploadingReplyView'
     ],
    function (Marionette, fancybox, template, UploadingAskView, UploadingReplyView) {
        "use strict";

        var footerView = Marionette.ItemView.extend({
            tagName: 'div',
            className: '',
            template : template,
            onRender: function() {
                var view = new UploadingAskView();
                var view = new UploadingReplyView();
            }
        });

        return footerView;
    });
