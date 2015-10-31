define(['app/views/Base', 'tpl!app/templates/message/MessageView.html', 'tpl!app/templates/message/MessageItemView.html'],
    function (View, template, itemTemplate) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            itemTemplate: itemTemplate,
            events: {
                'click .message .nav' : 'switchNav'
            },
            construct: function() {
                $("a.menu-bar-item").removeClass('active');

                this.listenTo(this.collection, "change", this.renderList);
            },
            renderList: function() {
                var template = this.itemTemplate;
                var el       = $('#message-item-list');

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
                this.onRender();
            },
            switchNav: function(e) {
                var el = e.currentTarget;
                $(el).addClass('nav-pressed').siblings().removeClass('nav-pressed');

                $('#message-item-list').empty();
                var type = $(e.currentTarget).attr('data');
                this.collection.reset();
                this.collection.data.type = type;
                this.collection.data.page = 0;
                this.collection.loadMore();
            }
        });
    });
