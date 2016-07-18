define(['tpl!app/views/user/progress/progress.html'],
    function (template) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .download-btn" : "fnDownload",
                "click .update-btn" : "fnUpdate"
            },
            onShow: function() {
                this.$('.imageLoad').imageLoad({scrop: true});
            },
            fnDownload:function(e) {
                var ask_id = $(e.currentTarget).attr('data-askId');
                var url = '/record';

                $.get(url,{
                    type: 1,
                    target: ask_id
                },function(){

                })
            },
            fnUpdate: function() {
                $("a[href=#update-popup]").attr('id','update-popup');

                $("a#update-popup").fancybox({
                        'padding': 0,
                    });
                $("a#update-popup").click();
                $("a#update-popup").removeAttr('id');
            }

        });
    });
