define(['app/views/Base', 'tpl!app/templates/index/IndexView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({

            template: template,
            onRender: function() {
            		$(".tupai-index").addClass("active").siblings().removeClass("active");
            },
        });
    });
