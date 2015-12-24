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
            $.post('user/follow', {
                uid: id
            }, function(data) {
                if(data.ret == 1) 
                    $(event.currentTarget).addClass('hide').siblings().removeClass('hide');
            });
        },
        // render: function() {
        //    var template = this.template;
        //    var el = $(this.el);
        //     this.collection.each(function(model){
        //         append(el, template(model.toJSON()));
        //     });
        //     this.onRender();
        // },
        // onRender: function() {
        //     $('a.menu-bar-search').click(function(){
        //         var keyword = $('#keyword').val();
        //         if(keyword != undefined && keyword != '') {
        //             location.href = '#search/all/'+keyword;
        //         }
        //         else {
        //             location.href = '#search/all';
        //         }
        //     });

        //     this.loadImage();
        // }
    });
});
