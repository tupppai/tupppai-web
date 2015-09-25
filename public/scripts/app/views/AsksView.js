define(['app/views/Base','tpl!app/templates/AsksView.html'],
	function(View, template) {
		"use strict"

		return View.extend({
			tagName: 'div',
			className: '',
			template: tamplate,
			construct: function() {
				var self = this;
				window.app.content.close();
				window.app.content.show(self);
			}

		})
	})