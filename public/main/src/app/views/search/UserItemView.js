define(['app/views/Base', 'app/collections/Users', 'tpl!app/templates/search/UserItemView.html'],
function (View, Users, template) {
    "use strict";
    return View.extend({
        tagName: 'div',
        className: '',
        template: template,
        collection: Users,
        events: {
            "click #attention" : "attention",
        },
        construct: function() {
            this.listenTo(this.collection, 'change', this.render);
            this.collection.loading();
        },
        attention: function(event) {
            var el = $(event.currentTarget);
            var id = el.attr("data-id");
            $.post('/user/follow', {
                uid: id
            }, function(data) {
                if(data.ret == 1) 
                    $(event.currentTarget).addClass('hide').siblings().removeClass('hide');
            });
        },
    });
});
