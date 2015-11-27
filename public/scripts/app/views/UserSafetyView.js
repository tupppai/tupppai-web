define(['common', 'app/models/User', 'app/views/Base', 'tpl!app/templates/UserSafetyView.html'],
    function (common, User,  View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            models: User,
            construct: function() {
                this.listenTo(this.model, "change", this.render);
            },

        });
    });
