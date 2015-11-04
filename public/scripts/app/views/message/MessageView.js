define([
        'app/views/Base', 
        'tpl!app/templates/message/MessageView.html', 
        'tpl!app/templates/message/MessageItemView.html'
        ],
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
                var self = this;
                $("a.menu-bar-item").removeClass('active');

                this.listenTo(this.collection, "change", this.renderList);

                self.scroll();
                self.collection.loadMore(self.showEmptyView);
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
                var self = this;
                var el = e.currentTarget;
                $(el).addClass('nav-pressed').siblings().removeClass('nav-pressed');

                $('#message-item-list').empty();
                var type = $(e.currentTarget).attr('data');
                this.collection.reset();
                this.collection.data.type = type;
                console.log( type );
                this.collection.data.page = 0;
                this.collection.loadMore(self.showEmptyView);
            },
            showEmptyView: function(data) {
                var self = this;
                if(data.data.page == 1 && data.length == 0) {
                    append($("#message-item-list"), ".emptyContentView");
                }
            }
        });
    });
