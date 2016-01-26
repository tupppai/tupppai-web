 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ChannelNavView.html',
        'superSlide'
       ],
    function (View, template, superSlide) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'nav-scroll clearfix',
            template: template,
            construct: function () {
                
                this.listenTo(this.collection, 'change', this.render);
                this.collection.loading();
            },
            onRender: function() {
                var type = $(".header-container").attr("data-type");
                 if(type == 'ask' ) {
                    $(".header-nav[data-id=1008]").trigger('click');
                } else if(type == 'reply') {
                    $(".header-nav[data-id=1007]").trigger('click');
                } else if(type) {
                    $(".header-nav[data-id="+ type +"]").trigger('click');
                } else {
                    $(".nav-scroll div:first").trigger('click');
                }
                $(".menu-bar-item[href='/#channel']").addClass('active');

                setTimeout(function() {
                    var length= $(".channel-header-nav").find(".present-nav").length;
                    if (length > 6) {
                        $(".channel-nav-left, .channel-nav-right").removeClass("blo"); 
                        $("#channelNav").slide({
                            easing: "easeInOutCubic",
                            titCell: "",
                            mainCell: ".nav-scroll ",
                            autoPage: true,
                            effect: "leftLoop",
                            autoPlay: true,
                            vis: 6,
                            delayTime: 500,
                            pnLoop: true,
                            interTime: 2500,
                            triggerTime: 150
                        });
                    };
                }, 1500);
            },
        });
    });