 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            // events: {
            //     "click .like_toggle" : 'likeToggleLarge',
            //     "click .download" : "download",
            // },
           
        });
    });
