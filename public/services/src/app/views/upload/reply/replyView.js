define(['tpl!app/views/upload/reply/reply.html', 'wx'],
    function (template) {
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
            	"click .uploadCancel": "emptyPic",
            	"click #fnSubmitDynamic": "fnSubmitDynamic",
            },
            emptyPic: function(e) {
		      	$("#fileList").text("");
				$(".holderBorder").show();
				$(".holderMain").show();
				$(".uploadCancel").addClass("hide");
            },
            fnSubmitDynamic:function() {
            	var uid = $("body").attr("data-uid");
            	var ask_id = $("body").attr("ask_id");
                var images = $('#append_image');
            	var imgLength = images[0].childElementCount;
            	var imgs = [];
                var upload_id = $("body").attr("upload_id");
        		var titleDynamic = $('.uploadDesc').val();
               for(var i = 0; imgLength > i;  ) {
		        	imgs[i] = images[0].childNodes[i].children[0].currentSrc;
		        	i++
			    }
			    var data = {
                    ask_id: ask_id,
			    	desc: titleDynamic,
			        upload_id: upload_id
			    }
			    if(titleDynamic.length > 0) {
				    $.post('/replies/save',data,function(rData){
			    		fntoast('发布成功','hide');
			    		setTimeout(function(){
			    		},1500)
			    		location.href = '#ask/detail/'+ ask_id;
				    })
			    } else {
		    		fntoast('请描述你的作品','hide');
			    }
            },
            fnUploadImage:function() {
			     wx.chooseImage({
			         count: 1, // 默认9
			         success: function (res) {
			             var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
			             var localId = localIds.toString();
			             var imageTpl = '<div class="clips-wrapper"><img src="'+localId+'" class="clips"></div>';
			             var titleDynamic = $('.uploadDesc').val();
                         $('#append_image').append(imageTpl);
			             var imgageCount = Number($('#image_count').text());
			             imgageCount++;
			             $('#image_count').text(imgageCount);
			             if(imgageCount == 1){
                            $(".uploadText").addClass("hide");
			             	$('#uploadWork').addClass('hide');
			             };
			             wx.uploadImage({
							localId: localId,
							isShowProgressTips: 1,
							success:function(res) {
								var serverId = res.serverId;
								var data = {
								 	media_id: serverId
                                }
								$.post('/v2/upload',data,function(data){
                                    $("body").attr("upload_id", data.upload_id);
                                    $(".confirm-none").addClass("confirm");
								})
							}
		                });
			         }
			     });
            }
         });
    });


