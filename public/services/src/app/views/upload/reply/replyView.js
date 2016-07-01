define(['tpl!app/views/upload/reply/reply.html', 'wx'],
    function (template, wx) {
        "use strict";

        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            onShow: function() {
            	$(".menuPs").addClass("hide");
            	title('发布作品');
            },
            events: {
            	"click #uploadWork": "fnUploadImage",
            	"click #fnSubmitDynamic": "fnSubmitDynamic",
            },
            // 上传图片
            fnSubmitDynamic:function() {
            	var ask_id = $("body").attr("ask_id"); //原图ID
                var upload_id = $("body").attr("upload_id");  //微信添加图片等时候返回的upload_id 
            	var category_id = $("body").attr("category_id"); //频道活动id
        		var titleDynamic = $('.uploadDesc').val(); //作品描述
			    var data = {
                    ask_id: ask_id,
					desc: titleDynamic,
			        upload_id: upload_id,
			        category_id: category_id
			    };

			    if(titleDynamic.length > 0) {
				    $.post('/v2/replies/save',data,function(rData){
						fntoast('发布成功','hide');
						if (category_id) {
                            redirect('#activity/index/1');//返回活动页面
							$("body").attr("desc", titleDynamic); //上传作品的描述 分享的时候用
							$("body").attr("reply_id", rData.id); //上传作品的作品id
						} else {
                            redirect('#detail/works/'+ ask_id);//跳链接返回详情页
						}
				    })
			    } else {
					fntoast('请描述你的作品','hide'); //弹窗
			    }
            },
            // 添加图片
            fnUploadImage:function() {
			     wx.chooseImage({
			         count: 1, // 默认9
			         success: function (res) {
			            var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
			            var localId = localIds.toString();
			            var imageTpl = '<div class="clips-wrapper"><img src="'+localId+'" class="clips"></div>';
			            var titleDynamic = $('.uploadDesc').val();
                        $('#append_image').append(imageTpl); 
                        $(".uploadText").addClass("hide"); //隐藏点击标签防止多次点击添加图片
						$('#uploadWork').addClass('hide');
			             wx.uploadImage({
							localId: localId,
							isShowProgressTips: 1,
							success:function(res) {
								var serverId = res.serverId;
								var data = {
									media_id: serverId
                                }
								$.post('/v2/upload',data,function(data){
									$("body").attr("img_url", data.image_url);
                                    $("body").attr("upload_id", data.upload_id)
                                    $(".confirm-none").addClass("confirm");
								})
							}
		                });
			         }
			     });
            }
         });
    });


