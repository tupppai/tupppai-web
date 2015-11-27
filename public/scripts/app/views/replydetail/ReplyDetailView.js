define([
        'app/views/Base', 
        'tpl!app/templates/replydetail/ReplyDetailView.html'
       ],
    function (View,  template, tmplate) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template

        });
    });
