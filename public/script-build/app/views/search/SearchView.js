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
                var keyword = $('#keyword').val();
                
	            if(keyword != undefined && keyword != '') {
	                location.href = '#search/'+ type +'/'+ keyword;
                }
                else {
                    location.href = '#search/'+ type;
	            }
            }
        });
    });
