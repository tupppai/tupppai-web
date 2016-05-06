define(['tpl!app/views/upload/reply/reply.html', 'wx'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            onShow: function() {
            	$(".menuPs").addClass("hide");
            },
            events: {
            	"change #fileElem": "fnUploadImage",
            	"click .uploadCancel": "emptyPic",
            },
            emptyPic: function(e) {
		      	$("#fileList").text("");
				$(".holderBorder").show();
				$(".holderMain").show();
				$(".uploadCancel").addClass("hide");
            },
            fnUploadImage:function() {
			     wx.chooseImage({
			         count: 1, // 默认9
			         success: function (res) {
			             var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
			             var localId = localIds.toString();
			             var imageTpl = '<div class="clips-wrapper"><img src="'+localId+'" class="clips"></div>';
			             $('#append_image').append(imageTpl);
			             var imgageCount = Number($('#image_count').text());
			             imgageCount++;
			             $('#image_count').text(imgageCount);
			             if(imgageCount == 3){
			             	$('#upload_image').addClass('hide');
			             }
			             wx.uploadImage({
							localId: localId,
							isShowProgressTips: 1,
							success:function(res) {
								var serverId = res.serverId;
								var data = {
								 	mediaid: serverId
								}
								$.post('getmedia',data,function(data){
								    var saveImage = '<div class="clips-wrapper"><img src="'+data.file+'" class="clips"></div>';
									$('#save_images').append(saveImage);
								})
							}
		                });
			         }
			     });
            }
         });
    });


