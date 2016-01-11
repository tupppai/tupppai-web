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
            events: {
                "mouseenter .demmand-contain": "channelDemmand",
                "mouseleave .demmand-contain": "channelDemmand",
            },
            channelDemmand: function(e) {
                if(e.type == "mouseenter") {
                    $(e.currentTarget).find(".demmand-position-top").stop(true, true).fadeIn(1000);
                }                
                if(e.type == "mouseleave") {
                    $(e.currentTarget).find(".demmand-position-top").stop(true, true).fadeOut(1000);
                }
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
                this.collection.loading();
            }
        });
    });
