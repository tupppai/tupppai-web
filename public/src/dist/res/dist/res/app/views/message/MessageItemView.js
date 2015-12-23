define([
        'app/views/Base', 
        'tpl!app/templates/message/MessageItemView.html'
        ],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
      
            construct: function() {
                $("a.menu-bar-item").removeClass('active');
                this.listenTo(this.collection, "change", this.render);
                this.scroll();
                this.collection.loading(this.showEmptyView);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#contentView div"), ".emptyContentView");
                } 
            },
        });
    });
