define(['app/views/Base','tpl!app/templates/ShowView.html'],
	function(View, template) {
		"use strict"

		return View.extend({
			tagName: 'div',
			className: '',
			template: template,
			construct: function() {
				var self = this;
				window.app.content.close();
				this.listenTo(this.model, 'change', this.render);
			}

		})
	})