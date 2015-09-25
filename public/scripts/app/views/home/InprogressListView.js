define(['app/views/home/ListView', 'app/collections/Asks', 'tpl!app/templates/home/InprogressItemView.html'],
    function (View, Asks, InprogressItemTemplate) {
        "use strict";

        var asks = new Asks;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: asks,
            template: InprogressItemTemplate
        });
    });
