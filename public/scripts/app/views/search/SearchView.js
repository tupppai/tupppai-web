define(['app/views/Base', 'tpl!app/templates/search/SearchView.html'],
         
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            template: template,
            events: {
            	'click .nav' : 'navBar',
            },
            navBar: function(e) {
            	var type = $(e.currentTarget).attr('data-type');

			    $(e.currentTarget).addClass('nav-botttom').siblings().removeClass('nav-botttom');
	            var keyword = $('#keyword').val();
	            if(keyword != undefined && keyword != '') {
	                location.href = '#search/'+ type +'/'+ keyword;
	            }
	            else {
	                location.href = '#search/'+ type;
	            }
	            switch(type) {
	            case 'user':
	                $('.correlation-content').addClass('hide');
	                $('.correlation-discuss').addClass('hide');
	                break;
	            case 'thread':
	                $('.correlation-user').addClass('hide');
	                $('.correlation-discuss').addClass('hide');
	                break;
	            case 'topic':
	                $('.correlation-user').addClass('hide');
	                $('.correlation-content').addClass('hide');
	                break;
	            default:
	                $('.correlation-user').removeClass('hide');
	                $('.correlation-discuss').removeClass('hide');
	                $('.correlation-content').removeClass('hide');
	                break;
	            }
            }
        });
    });
