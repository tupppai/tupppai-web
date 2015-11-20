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
             var self = this;
            $(".inprogress-popup").fancybox({
                     afterShow: function(){
                        $('.reply-uploading-popup').unbind('click').bind('click', self.askImageUrl);
                     }
                });   
            },
            askImageUrl:function(e) {
                var ask_id = $(e.currentTarget).attr('ask-id');
                $('#reply-uploading-popup').attr('ask-id', ask_id);
                var askImageUrl = $(e.currentTarget).parent().siblings('.ask-image').find('img').attr('src');
                $('#ask_image img').attr('src', askImageUrl);
            }
        });
    });
