define([
		'app/views/base', 
		'tpl!app/views/selectmale/selectAvatarView.html', 
	   ],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
	     	initialize: function() {
		        this.listenTo(this.model, 'change', this.render);
		        this.model.fetch();
	     	}
	        
        });
    });
