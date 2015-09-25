define(['app/views/Base','tpl!app/templates/CommentView.html'],
	function(View, template) {
		"use strict"

		return View.extend({
			tagName: 'div',
			className: '',
			template: template,
			construct: function() {
				var self = this;
				window.app.content.close();
				window.app.content.show(self);
			}

		})
	});