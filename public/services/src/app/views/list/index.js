define(['app/views/ask/index/indexView'], function (itemView) {
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
