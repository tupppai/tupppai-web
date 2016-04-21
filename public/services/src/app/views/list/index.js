define(['app/views/index/item'], function (itemView) {
    "use strict";
    
    return window.app.list.extend({
        childView: itemView,
        childEvents: {
        },
        initialize: function() {
            //this.listenTo(this.model, 'change', this.render);
        }
    });
});
