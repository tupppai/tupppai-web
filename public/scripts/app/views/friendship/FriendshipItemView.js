define([
        'app/views/Base', 
        'app/collections/Users', 
        'tpl!app/templates/friendship/FriendshipItemView.html'
       ],
    function (View, Users, template) {
        "use strict";
        
        return View.extend({
            collection: Users,
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .attention-btn-pressed' : 'attention'
            },
            attention: function(e) {
                var id = $(e.currentTarget).attr("data-id");
                $.post('user/follow', {
                    uid: id
                }, function(data) {
                    if(data.ret == 1) 
                    $(e.currentTarget).addClass('hide');
                });
            },
            construct: function() {
                var self = this;
                $("a.menu-bar-item").removeClass('active');
                var type = $(window.app.content.el).attr('data-type');
                this.highLight(type);

                this.listenTo(this.collection, "change", this.render);
                self.scroll();
                this.collection.loading(self.showEmptyView);
            },
            highLight: function(type) {
                $(".nav[data-type='"+type+"']").addClass('firendship-nav-pressed').siblings().removeClass('firendship-nav-pressed');
            },
            showEmptyView: function(data) {
                var self = this;
                if(data.length == 0) {
                    append($("#friendshipItemView"), ".emptyContentView");
                }
            }
        });
    });
