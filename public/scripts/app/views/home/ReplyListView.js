define([
        'app/views/home/HomeView', 
        'imagesLoaded',
        'app/collections/Replies', 
        'tpl!app/templates/home/ReplyItemView.html',
       ],
    function (View, imagesLoaded, Replies, ReplyItemTemplate) {
        "use strict";

        var replies = new Replies;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: replies,
            template: ReplyItemTemplate,
            onRender: function() {
                $('#load_reply').addClass('designate-nav').siblings().removeClass('designate-nav');

                this.loadImage();
            },
   
        });
    });
