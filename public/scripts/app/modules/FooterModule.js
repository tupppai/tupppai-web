define([
        'marionette',
        'fancybox',  
        'validationEnginezh',  
        'validationEngine',  
        'tpl!app/templates/FooterView.html',
        'app/views/upload/UploadingAskView',
        'app/views/upload/UploadingReplyView',
        'app/views/upload/InprogressView'
     ],

    function (Marionette, fancybox, validationEnginezh, validationEngine,  template, UploadingAskView, UploadingReplyView,  InprogressView) {
        "use strict";

        var footerView = Marionette.ItemView.extend({
            tagName: 'div',
            className: '',
            template : template,

            onRender: function() {
                var view = new UploadingAskView();
                var view = new UploadingReplyView();
                var view = new InprogressView();
            }
        });
        return footerView;
    });
