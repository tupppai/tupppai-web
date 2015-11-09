define(['app/views/Base', 'tpl!app/templates/message/MessageView.html'],
         
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .message .nav' : 'switchNav',
            },
            construct: function() {
                $("a.menu-bar-item").removeClass('active');
            },
            switchNav: function(e) {
                var self = this;
                var el = e.currentTarget;
                $(el).addClass('nav-pressed').siblings().removeClass('nav-pressed');

                $('#message-item-list').empty();
                var type = $(e.currentTarget).attr('data');
                this.collection.reset();

                this.collection.data.type = type;
                this.collection.data.page = 0;
                this.collection.loading(self.showEmptyView);
            },
            showEmptyView: function(data) {
                var self = this;
                if(self.data.page == 1 && data.length == 0) {
                    append($("#message-item-list"), ".emptyContentView");
                }
            }
        });
    });
