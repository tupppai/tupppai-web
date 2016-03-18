define(['app/views/base', 'tpl!app/views/uploadagain/UploadAgainView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	'click #uploadImage': "uploadImage"
            },
            uploadImage:function() {
            	var effect_id = $("#uploadImage").attr("effectId");; //效果ID
                var boy_id =  $("#uploadImage").attr("boyId");; //男神ID
                wx_choose_image(boy_id, effect_id);
            },
            onShow: function() {
                var url = decodeURI(location.href); //编译url上的乱码中文
                var tmp1 = url.split("?")[1]; //获取到？后面的

                var reason = tmp1.split("&")[0]; //获取&之前的
                var reasonText = reason.split("=")[1];  //获取＝后面的值

                var descText = $("body").attr("data-desc"); //获取&之后的
                // var descText = desc.split("=")[1];  //获取＝后面的值

                var boy_id = descText.split("-")[0]; //获取desc后面的－
                var effect_id = descText.split("-")[1];

                $("#uploadImage").attr("boyId", boy_id);
                $("#uploadImage").attr("effectId", effect_id);

               $(".reason").html(reasonText)
            }
        });
    });