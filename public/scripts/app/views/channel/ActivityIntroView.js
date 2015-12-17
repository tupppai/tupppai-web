 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ActivityIntroView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-reply-container grid',
            template: template,
            construct: function() {
                this.listenTo(this.model, 'change', this.render);
            },            
        });
    });
