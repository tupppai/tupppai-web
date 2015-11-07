define(['app/views/Base', 'app/collections/Friendships', 'tpl!app/templates/search/UserItemView.html'],
function (View, Friendships, template) {
    "use strict";
   var friendships = new Friendships;
    return View.extend({
        tagName: 'div',
        className: '',
        template: template,
        collection: friendships,
        events: {
            "click #attention" : "attention",
        },

        construct: function() {
            var self = this;
            this.listenTo(this.collection, 'change', this.render);
            self.collection.loadMore();
        },
        attention: function(event) {
            var el = $(event.currentTarget);
            var id = el.attr("data-id");
            $.post('user/follow', {
                uid: id
            }, function(data) {
                if(data.ret == 1) 
                    $(event.currentTarget).addClass('hide').siblings().removeClass('hide');
            });
        },

        render: function() {
           var template = this.template;
           var el = $(this.el);
            this.collection.each(function(model,a,b){
                var html = template(model.toJSON());
                el.append( html);
            });
            this.onRender();
        }
    });
});
