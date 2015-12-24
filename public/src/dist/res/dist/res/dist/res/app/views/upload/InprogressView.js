define([
        'underscore',
        'app/views/Base',
        'tpl!app/templates/upload/InprogressView.html'
       ],
    function (_, View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() {
                $(".inprogress-popup").fancybox({
                    afterShow: function(){
                        $('.reply-uploading-popup').unbind('click').bind('click', this.askImageUrl);
                    }
                 }); 
            },
            askImageUrl:function(e) {
            }
        });
    });
