define([
        'app/views/Base', 
        'app/collections/Inprogresses', 
        'tpl!app/templates/homepage/HomeCollectionView.html'
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
                this.listenTo(this.collection, 'change', this.render);

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
