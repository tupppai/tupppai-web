 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelFoldView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-fold grid',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
                this.collection.loading();
            }
           
        });
    });
