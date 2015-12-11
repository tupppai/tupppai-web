define(['app/views/Base', 'tpl!app/templates/index/IndexView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({

            template: template,
            events: {
            	"click .scrollTop-icon" : "scrollTop"
            },
            onRender: function() {
            	$(".tupai-index").addClass("active").siblings().removeClass("active");
                setTimeout(function(){
                    var id = $("body").attr("data-uid");
                    if( id ) {
                        $(".login-popup").addClass("hide");
                        $(".ask-uploading-popup-hide").removeClass('hide');
                    } else {
                        $(".ask-uploading-popup-hide").addClass('hide');
                        $(".login-popup").removeClass("hide");
                    }
                },500);
                

            },
            scrollTop:function(e) {
                $("body").scrollTop(0);
            },
        });
    });
