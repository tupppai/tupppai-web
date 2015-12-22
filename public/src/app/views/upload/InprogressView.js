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
            onRender:function() {
                debugger;
                $('.reply-uploading-popup').live('click', function(e){
                    var ask_id = $(e.currentTarget).attr('ask-id');
                    $('#reply-uploading-popup').attr('ask-id', ask_id);
                    var askImageUrl = $(e.currentTarget).parent().siblings('.ask-image').find('img').attr('src');
                    $('#ask_image img').attr('src', askImageUrl);
                    debugger;
                });
            },
            askImageUrl:function(e) {
            }
        });
    });
