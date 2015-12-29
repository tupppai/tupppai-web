 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelFoldView.html'
       ],
    function (View, template ) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-fold',
            template: template,
            events: {
                "mouseover .long-pic": "channelWidth",
                "mouseleave .long-pic": "channelWidth",
            },
            channelWidth: function(e) {
                if(e.type == "mouseover") {
                    $(e.currentTarget).find(".view-details").animate({
                        width: "20px"
                    }, 500);
                }
                if(e.type == "mouseleave") {
                    $(e.currentTarget).find(".view-details").stop(true, true).animate({
                        width: "0px"
                    }, 500);
                }
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
            },
 
        });
    });
