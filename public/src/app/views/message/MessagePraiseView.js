define(['app/views/Base', 'tpl!app/templates/message/MessagePraiseView.html'],
         
    function (View, template ) {
        "use strict";

        return View.extend({
            tagName: 'div',
            className: 'channel-fold',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
            },
 
        });
    });
