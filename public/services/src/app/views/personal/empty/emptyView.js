define(['tpl!app/views/personal/empty/empty.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            onShow: function() {
                var tapTapy = $("body").attr("tapTapy")
	            var clickId = $(".header-portrait").attr("data-id");
	            var currentId = $('body').attr("data-uid");

                if(clickId == currentId) {
                    $(".own").removeClass("hide");
                    if(tapTapy == 'ask') {
                        $(".empty-p").text("暂时没有发布求P");
                        $(".empty-buttom").removeClass("hide").text("马上求P").attr("href", "#upload/ask");
                    } else if(tapTapy == 'inprogresses') {
                        $(".empty-p").text("暂时没有添加帮P");
                        $(".empty-buttom").removeClass("hide").text("求P大厅").attr("href", "#original/index");
                    } else if(tapTapy == "replies") {
                        $(".empty-p").text("暂时没有发布作品");
                        $(".empty-buttom").addClass("hide");
                    }
                } else {
                    $(".ta").removeClass("hide");
                    $(".empty-buttom").addClass("hide");
                    if(tapTapy == 'ask') {
                        $(".empty-p").text("暂时没有发布求P");
                    } else if(tapTapy == 'inprogresses') {
                        $(".empty-p").text("暂时没有添加帮P");
                    } else if(tapTapy == "replies") {
                        $(".empty-p").text("暂时没有发布作品");
                    }
                }

            }
        });
    });


