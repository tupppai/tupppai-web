define([
        'app/views/home/HomeView', 
        'imagesLoaded',
        'tpl!app/templates/home/ReplyItemView.html',
       ],
    function (View, imagesLoaded, ReplyItemTemplate) {
        "use strict";


        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            template: ReplyItemTemplate,
            onRender: function() {
                $('#load_reply').addClass('designate-nav').siblings().removeClass('designate-nav');

                this.loadImage();
            },
   
        });
    });
