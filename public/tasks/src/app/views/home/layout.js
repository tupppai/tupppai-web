define([
	'marionette', 
	'tpl!app/views/test/index.html'],
    function (Marionette, template) {
        "use strict";
        
        //marionette 1.0.0版本Marionette.Layout 此后版本为Marionette.LayoutView
        return Marionette.Layout.extend({
            template: template,
            regions: {
            	header: '#home-header',
            	content: '#home-content'
            },
            onRender: function() {
            	
            }
        });
    });
