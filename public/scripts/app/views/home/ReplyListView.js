define(['app/views/home/ListView', 'app/collections/Replies', 'tpl!app/templates/home/ReplyItemView.html'],
    function (View, Replies, ReplyItemTemplate) {
        "use strict";

        var replies = new Replies;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: replies,
            template: ReplyItemTemplate
        });
    });
