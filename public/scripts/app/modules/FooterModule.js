define([
        'marionette',
        'fancybox',  
        'app/collections/Inprogresses', 
        'tpl!app/templates/FooterView.html',
        'app/views/upload/UploadingAskView',
        'app/views/upload/UploadingReplyView',
        'app/views/upload/InprogressItemView',
        'app/views/upload/InprogressView'
     ],
    function (Marionette, fancybox, Inprogresses, template, UploadingAskView, UploadingReplyView,InprogressItemView, InprogressView) {
        "use strict";

        var footerView = Marionette.ItemView.extend({
            tagName: 'div',
            className: '',
            template : template,



            onRender: function() {

            var inprogresses = new Inprogresses;
            var inprogressItemView = new Backbone.Marionette.Region({el:"#InprogressItemView"});
            var view = new InprogressItemView({
                collection: inprogresses
            });
            inprogressItemView.show(view);
            
                var view = new UploadingAskView();
                var view = new UploadingReplyView();
                var view = new InprogressView();
            }
        });

        return footerView;
    });
