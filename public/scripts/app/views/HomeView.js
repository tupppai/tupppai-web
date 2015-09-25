define(['app/views/Base', 'tpl!app/templates/HomeView.html',
                          'tpl!app/templates/home/AskItemView.html',
                          'tpl!app/templates/home/InporgressItemView.html',
                          'tpl!app/templates/home/ReplyItemView.html'],
    function (View, homeTemplate, askItem, inprogressItem, replyItem) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: homeTemplate,
            construct: function () {
                var self = this;
                window.app.content.close();
                window.app.content.show(self);

            }
        });
    });
