define([
        'app/views/Base', 
        'app/collections/Friendships', 
        'tpl!app/templates/friendship/FriendshipView.html',
        'tpl!app/templates/friendship/FriendshipItemView.html'
       ],
    function (View, Friendships, template,followItemTemplate) {
        "use strict";
        
        return View.extend({
            collection: Friendships,
            tagName: 'div',
            className: '',
            template: template,
            followItemTemplate: followItemTemplate,
            events: {
                'click .friendship-header .nav' : 'switchNav',
                'click .attention-btn-pressed' : 'attention'
            },
            attention: function(e) {
                $(e.currentTarget).addClass('hide');
            },
            construct: function() {
                var self = this;
                $("a.menu-bar-item").removeClass('active');
                this.listenTo(this.collection, "change", this.followRenderList);

                self.scroll();
                self.collection.loadMore();
                self.collection.trigger('change');
            },
            followRenderList: function() {
                var template = this.followItemTemplate;
                var el       = $('#friendshipItenView');
                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    el.append(html);
                });
                this.onRender();
            }, 
            switchNav: function(e) {
                var self = this;
                var el = e.currentTarget;

                $(el).addClass('firendship-nav-pressed').siblings().removeClass('firendship-nav-pressed');

                var type = $('.firendship-nav-pressed').attr('data-type');
                var uid  = $(window.app.content.el).attr('data-uid');

            },
            highLight: function() {
                var type = $(window.app.content.el).attr('data-type');
                //todo
            }
        });
    });
