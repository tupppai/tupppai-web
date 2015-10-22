define([
        'app/views/home/HomeView', 
        'imagesLoaded',
        'app/collections/Replies', 
        'tpl!app/templates/home/ReplyItemView.html',
       ],
    function (View, imagesLoaded, Replies, ReplyItemTemplate) {
        "use strict";

        var reply = new Replies;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: reply,
            template: ReplyItemTemplate,
            onRender: function() {
                $('#load_reply').addClass('designate-nav').siblings().removeClass('designate-nav');

                this.loadImage();
            },
   
        });
    });
