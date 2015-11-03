define(['app/views/Base', 'app/models/User', 'tpl!app/templates/FriendshipView.html'],
    function (View, User, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            construct: function () {

            },
           
        });
    });
