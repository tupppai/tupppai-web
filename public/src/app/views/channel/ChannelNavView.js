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
                        $(".channel-header").slide({
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
                            triggerTime: 550
                        });
                    };
                }, 1500);
                if($(window).width() < 640) {
                   pageResponse({
                        selectors: '.channel-contain',     //模块的类名，使用class来控制页面上的模块(1个或多个)
                        mode : 'auto',     // auto || contain || cover 
                        width : '1180',      //输入页面的宽度，只支持输入数值，默认宽度为320px
                        height : '80'      //输入页面的高度，只支持输入数值，默认高度为504px
                    })
                }
            },
        });
    });