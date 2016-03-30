define(['app/views/base', 'tpl!app/views/selectmale/selectButtonView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	'click #selectionButton':'selctionStatus'
            },
            initialize: function() {
        	     	 this.listenTo(this.model, 'change', this.render);
   					 this.model.fetch({
   					 	 success:function(res) {

		                }
   					 });
            },
            selctionStatus: function(){
		        var code = $('#selectionButton').attr('data-code');
		        
                if(code == 1) {
                    location.href = 'http://' + location.hostname + '/boys/uploadsuccess/uploadsuccess';
                } 
                //求P成功有作品
                if(code == 2) {
                    location.href = 'http://' + location.hostname + '/boys/obtainsuccess/obtainsuccess';
                } 
                //求P被拒绝
                if(code == -1) {
                    location.href = 'http://' + location.hostname + '/boys/uploadagain/uploadagain';
                } 	
                if( code == -2 ){
                	var activeIndex = $('.pic-box').attr('index');
                    location.href = 'http://' + location.hostname + '/boys/getavatar/getavatar#'+activeIndex;
                }   
                if( code == -3 ){
                    location.href = 'http://' + location.hostname + '/boys/uploadsuspend/uploadsuspend#';
                }
            }
	        
        });
    });
