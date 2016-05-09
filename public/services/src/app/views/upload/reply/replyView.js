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
            	var images = $('#append_image');
            	var imgLength = images[0].childElementCount;
            	var imgs = [];
        		var titleDynamic = $('.uploadDesc').val();
            	debugger;
        	    for(var i = 0; imgLength > i;  ) {
			        	imgs[i] = images[0].childNodes[i].children[0].currentSrc;
			        	i++
			    }
			    var data = {
			    	title: titleDynamic,
			    	image_urls: imgs,
			    	upload_select: 2
			    }
			    if(titleDynamic == '') {
			    	fntoast('内容不能为空','hide')
			    	return false
			    }
			    if(!imgs) {
			    	fntoast('图片不能为空','hide')
			    	return false
			    }
			    $.post('/upload',data,function(rData){
			    	if( rData[0] == '写入成功') {
				    		fntoast('发布成功','hide');
			    		setTimeout(function(){
			    		},1500)
			    	}
			    })
            },
            fnUploadImage:function() {
			     wx.chooseImage({
			         count: 1, // 默认9
			         success: function (res) {
			         	 var ask_id = $("body").attr("ask_id");
			             var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
			             var localId = localIds.toString();
			             var imageTpl = '<div class="clips-wrapper"><img src="'+localId+'" class="clips"></div>';
			             $('#append_image').append(imageTpl);
			             var imgageCount = Number($('#image_count').text());
			             imgageCount++;
			             $('#image_count').text(imgageCount);
			             if(imgageCount == 1){
                            $(".uploadText").addClass("hide");
			             	$('#uploadWork').addClass('hide');
			             };
                         debugger;
			             wx.uploadImage({
							localId: localId,
							isShowProgressTips: 1,
							success:function(res) {
								var serverId = res.serverId;
								var data = {
<<<<<<< HEAD
								 	media_id: serverId
                                }
								$.post('/v2/upload',data,function(data){
								    var saveImage = '<div class="clips-wrapper"><img src="'+data.file+'" class="clips"></div>';
								debugger; alert(1, data);
=======
								 	mediaid: serverId,
                                }
								$.post('/v2/upload',data,function(data){
								    var saveImage = '<div class="clips-wrapper"><img src="'+data.file+'" class="clips"></div>';
									debugger;	
									alert("ok", data);
>>>>>>> 71e9c196c6f5413b2b38d5719b1efda94d01672b
                                    $('#save_images').append(saveImage);
								})
							}
		                });
			         }
			     });
            }
         });
    });


