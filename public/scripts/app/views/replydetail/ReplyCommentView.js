define([
        'app/views/Base', 
        'tpl!app/templates/replydetail/ReplyCommentView.html'
       ],
    function (View,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
        });
    });
