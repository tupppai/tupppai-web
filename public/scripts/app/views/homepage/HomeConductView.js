define([
        'app/views/Base', 
        'app/collections/Inprogresses', 
        'tpl!app/templates/homepage/HomeConductView.html'
       ],
    function (View, Inprogresses, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collections: Inprogresses,
            events: {
                "click .download" : "download",
                "click .reply-uploading-popup" : "askImageUrl"
            },

            construct: function() {
                var self = this;
                var uid = $(".menu-nav-reply").attr("data-id");
                self.listenTo(self.collection, 'change', self.render);
                self.scroll();
                self.collection.reset();
                self.collection.data.uid = uid;
                self.collection.data.page = 0;
                self.collection.loading();

            var inProgressPopup = $(".inprogress-popup");
                $(".inprogress-popup").fancybox({
                     afterShow: function(){
                        $('.conduct-upload').unbind('click').bind('click', askImageUrl);
                     }
                }); 
            },
           askImageUrl:function(e) {   
                var ask_id = $(e.currentTarget).attr('ask-id');
                $('#reply-uploading-popup').attr('ask-id', ask_id);
                var askImageUrl = $(e.currentTarget).parents('.conduct-right').siblings(".conduct-pic").find('img').attr('src');

                $('#ask_image img').attr('src', askImageUrl);
            }
        });
    });
