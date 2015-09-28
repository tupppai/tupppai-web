define(['app/views/home/ListView', 'app/collections/Asks', 'tpl!app/templates/home/AskItemView.html'],
    function (View, Asks, askItemTemplate) {
        "use strict";

        var asks = new Asks;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: asks,
            template: askItemTemplate,
      


        });
    });
