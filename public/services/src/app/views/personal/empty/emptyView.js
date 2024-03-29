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
                var currentId = window.app.user.get('uid');
                if(clickId == currentId) {
                    $(".own").removeClass("hide");
                    $(".empty-buttom").removeClass("hide").text("马上求P").attr("href", "#upload/ask");
                    if(tapTapy == 'ask') {
                        $(".empty-p").text("暂时没有发布求P");
                        $(".empty-buttom").removeClass("hide").text("马上求P").attr("href", "#upload/ask");
                    } 
                    if(tapTapy == 'inprogresses') {
                        $(".empty-p").text("暂时没有添加帮P");
                        $(".empty-buttom").removeClass("hide").text("求P大厅").attr("href", "#original/index");
                    } 
                    if(tapTapy == "replies") {
                        $(".empty-p").text("暂时没有发布作品");
                        $(".empty-buttom").addClass("hide");
                    }
                } else {
                    $(".ta").removeClass("hide");
                    $(".empty-buttom").addClass("hide");
                    if(tapTapy == 'ask') {
                        $(".empty-p").text("暂时没有发布求P");
                    }  
                    if(tapTapy == 'inprogresses') {
                        $(".empty-p").text("暂时没有添加帮P");
                    }
                    if(tapTapy == "replies") {
                        $(".empty-p").text("暂时没有发布作品");
                    }
                }

            }
        });
    });


