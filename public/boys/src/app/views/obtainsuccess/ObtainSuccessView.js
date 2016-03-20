define(['app/views/base', 'tpl!app/views/obtainsuccess/ObtainSuccessView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            initialize:function() {
                this.listenTo(this.model, 'change', this.render);
                this.model.fetch();
            },
            onShow: function() {

                // 微信好友文案修改
                var options = {};
                options.code = $('body').attr('data-code');
                share_friend(options,function(){},function(){})
                share_friend_circle(options,function(){},function(){})
                
                // var url = location.href;
                var dataDesc = $('#desc').attr('data-desc');
                alert( dataDesc );
                // var tmp1 = url.split("#")[1]; //获取到？后面的
                if(dataDesc) {
                var boy_id = dataDesc.split("-")[0]; //男神ID
                var effect_id = dataDesc.split("-")[1]; //效果ID
                }

                $(".avatar-name").eq(boy_id).removeClass("none").siblings(".avatar-name").addClass("none");  //谁的效果说明
                $(".avatar-name").eq(boy_id).find("img").eq(effect_id).removeClass("none").siblings("img").addClass("none");  //谁的哪种效果说明

                // $(".avatar-effect").eq(boy_id).removeClass("none").siblings(".avatar-effect").addClass("none");		//谁的效果
                // $(".avatar-effect").eq(boy_id).find("img").eq(effect_id).removeClass("none").siblings("img").addClass("none");	//谁的哪种效果

                $(".author").find("img").eq(effect_id).removeClass("none").siblings("img").addClass("none");  //效果作者
            }
        });
    });