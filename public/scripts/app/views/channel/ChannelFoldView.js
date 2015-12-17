 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelFoldView.html'
       ],
    function (View, template, ChannelFoldView ) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-fold',
            template: template,
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
            }
        });
    });
