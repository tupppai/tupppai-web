define(['app/views/Base', 'tpl!app/templates/show/ShowReplyView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function() { 
                this.listenTo(this.collection, 'change', this.render);

                this.collection.loading();


                //bug: 如果有多个页面切换，scroll事件会被注销掉
                var self = this;
                setTimeout(function() {
                    self.scroll();
                }, 1000);
            }
        });
    });
