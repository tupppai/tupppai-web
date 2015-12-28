 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelDemandView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'father-grid',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
                this.collection.loading();
            }
        });
    });
