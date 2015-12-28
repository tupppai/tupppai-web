define([
        'app/views/Base', 
        'tpl!app/templates/trend/TrendView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .super-like" : "superLike"
            },
            construct: function () {
                this.listenTo(this.collection, 'change', this.render);
                this.scroll();
                this.collection.loading(this.showEmptyView);
            },
            onRender: function() {
                $('.download').unbind('click').bind('click',this.download);
                this.loadImage(); 

                $(window).scroll(function() {
                    var scrollTop = $(window).scrollTop();
                    console.log(scrollTop);
                    if(scrollTop > 700) {
                        $(".width-hide").fadeIn(1000);
                    } else {
                        $(".width-hide").fadeOut(1000);
                    }
                });
            }

        });
    });
