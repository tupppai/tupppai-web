define([
	'marionette', 
	'app/views/home/layout',
	'app/views/home/subview1',
	'app/views/home/subview2',
	'app/models/home',
	'app/collections/home'
	], function (Marionette, Layout, Subview1, Subview2, Home, Homes) {
    	"use strict";

    	return function() {
    		//数据拉取在controller层，布局用Layout控制
        	var view = new Layout();

        	var home = new Home();
        	var homeView1 = new Subview1({
        		model: home
        	});
        	model.fetch();
        	view.header.show(homeView1);

        	var homes = new Homes();
        	var homeView2 = new Subview2({
        		collection: homes
        	});
        	homes.loading();
        	view.content.show(homeView2);

        	window.app.content.show(view);
    	};
	});
