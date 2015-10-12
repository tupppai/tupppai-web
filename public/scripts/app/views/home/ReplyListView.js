define(['app/views/home/HomeView', 'app/collections/Replies', 'tpl!app/templates/home/ReplyItemView.html'],
    function (View, Replies, ReplyItemTemplate) {
        "use strict";

        var replies = new Replies;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: replies,
            template: ReplyItemTemplate,
            render: function() {
                $('#load_reply').addClass('designate-nav').siblings().removeClass('designate-nav');
            },
   
        });
    });
