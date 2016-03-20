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
            initialize:function() {
                this.listenTo(this.model, 'change', this.render);
                this.model.fetch();
            },
            uploadImage:function() {
            	var effect_id = $("#uploadImage").attr("effectId");; //效果ID
                var boy_id =  $("#uploadImage").attr("boyId");; //男神ID
                wx_choose_image(boy_id, effect_id);
            },
            onShow: function() {

                // 微信好友文案修改
                var options = {};
                options.code = $('body').attr('data-code');
                share_friend(options,function(){},function(){})
                share_friend_circle(options,function(){},function(){})
                
                // var url = decodeURI(location.href); //编译url上的乱码中文
                // var tmp1 = url.split("?")[1]; //获取到？后面的 

                // var reason = tmp1.split("&")[0]; //获取&之前的
                var reasonText = $("body").attr("data-reason");  //获取＝后面的值

                var descText = $("body").attr("data-desc"); //获取&之后的
                // var descText = desc.split("=")[1];  //获取＝后面的值

                var boy_id = descText.split("-")[0]; //获取desc后面的－
                var effect_id = descText.split("-")[1];

                // $("#uploadImage").attr("boyId", boy_id);
                // $("#uploadImage").attr("effectId", effect_id);

               // $(".reason").html("拒绝理由：" + reasonText);
            }
        });
    });