define([
        'app/views/Base', 
        'app/models/Like', 
        'app/models/Base',
        'tpl!app/templates/DynamicsView.html'
       ],
    function (View, Like,ModelBase, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                'click .like_toggle' : 'likeToggle',
                'click .collection_toggle' : 'collectToggle'
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                self.scroll();
                self.collection.loading();
            },
            onRender: function() {
                $('.download').unbind('click').bind('click',this.download);

                this.loadImage(); 
            }
        });
    });
