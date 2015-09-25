define(['app/views/home/ListView', 'app/collections/Asks', 'tpl!app/templates/home/ReplyItemView.html'],
    function (View, Asks, ReplyItemTemplate) {
        "use strict";

        var asks = new Asks;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: asks,
            template: ReplyItemTemplate
        });
    });
