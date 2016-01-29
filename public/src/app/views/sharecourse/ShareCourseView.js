 define([
        'app/views/Base',
        'tpl!app/templates/sharecourse/ShareCourseView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() { 
                this.listenTo(this.collection, 'change', this.render);
                // this.scroll();
                this.collection.loading(this.showEmptyView);
            }, 
            onRender: function(){ 
                var htmlWidth = $('html').width();
                if (htmlWidth >= 750) {
                    $("html").css({
                        "font-size" : "28px"
                    });
                } else {
                    $("html").css({
                        "font-size" :  28 / 750 * htmlWidth + "px"
                    });
                };
                $(window).resize(function() {
                    var htmlWidth = $('html').width();
                    if (htmlWidth >= 750) {
                        $("html").css({
                            "font-size" : "28px"
                        });
                    } else {
                        $("html").css({
                            "font-size" :  28 / 750 * htmlWidth + "px"
                        });
                    }
                });
            },
           
        });
    });
