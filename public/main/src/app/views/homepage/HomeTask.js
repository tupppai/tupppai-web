define([
        'app/views/Base', 
        'tpl!app/templates/homepage/HomeTask.html'
       ],
    function (View,template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .download" : "download",
                "click .reply-uploading-popup" : "askImageUrl"
            },

            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
                var inProgressPopup = $(".inprogress-popup");
                    $(".refuse-uploading-popup").fancybox({
                        
                    }); 
                    $(".inprogress-popup").fancybox({
                            afterShow: function(){}
                    });
            },
           askImageUrl:function(e) {   
                var ask_id = $(e.currentTarget).attr('ask-id');
                var categorty_id = $(e.currentTarget).find(".categorty-id").attr("data-id");
                $('#reply-uploading-popup').attr('ask-id', ask_id);
                $('#reply-uploading-popup').attr('data-id', categorty_id);
                var askImageUrl = $(e.currentTarget).parents('.conduct-right').siblings(".conduct-pic").find('img').attr('src');

                $('#ask_image img').attr('src', askImageUrl);
            }
        });
    });
