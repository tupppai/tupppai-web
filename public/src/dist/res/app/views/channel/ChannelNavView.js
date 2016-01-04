 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelNavView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'nav-scroll clearfix',
            template: template,
            construct: function () {
                debugger;
                this.listenTo(this.collection, 'change', this.render);
                this.collection.loading();
            }
        });
    });