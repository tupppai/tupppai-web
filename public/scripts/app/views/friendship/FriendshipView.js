define([
        'app/views/Base', 
        'app/collections/Users', 
        'tpl!app/templates/friendship/FriendshipView.html',
       ],
    function (View, Users, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .friendship-header .nav' : 'switchNav'
            },
            construct: function() {
                var self = this;
                $("a.menu-bar-item").removeClass('active');
            }, 
            switchNav: function(e) {
                var self = this;
                var el = e.currentTarget;

                var type = $(e.currentTarget).attr('data-type');
                var uid  = $(window.app.content.el).attr('data-uid');
                location.href = '#friendship/'+type+'/'+uid;
            }
        });
    });
