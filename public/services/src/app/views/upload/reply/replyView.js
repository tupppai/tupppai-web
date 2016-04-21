define(['app/views/base', 'tpl!app/views/upload/reply/reply.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            onShow: function() {
            	$(".menuPs").addClass("hide");
            },
            events: {
            	"change #fileElem": "handleFiles",
            	"click .uploadCancel": "emptyPic",
            },
            emptyPic: function(e) {
		      	$("#fileList").text("");
				$(".holderBorder").show();
				$(".holderMain").show();
				$(".uploadCancel").addClass("hide");
            },
            handleFiles:function(e) {
        		window.URL = window.URL || window.webkitURL;
				var fileElem = document.getElementById("fileElem"),
			    	fileList = document.getElementById("fileList");

				var files = e.target.files,
					img = new Image();
				if(window.URL){
					//File API
				      img.src = window.URL.createObjectURL(files[0]); //创建一个object URL，并不是你的本地路径
				      img.onload = function(e) {
				         window.URL.revokeObjectURL(this.src); //图片加载后，释放object URL
				      }
				      $("#fileList").text("");
				      fileList.appendChild(img);
				      $(".holderBorder").hide();
				      $(".holderMain").hide();
				      $(".uploadCancel").removeClass("hide");
				}else if(window.FileReader){
					//opera不支持createObjectURL/revokeObjectURL方法。我们用FileReader对象来处理
					var reader = new FileReader();
					reader.readAsDataURL(files[0]);
					reader.onload = function(e){
						img.src = this.result;
						img.width = 200;
						$("#fileList").text("");
						fileList.appendChild(img);
				      	$(".holderBorder").hide();
				      	$(".holderMain").hide();
				      	$(".uploadCancel").removeClass("hide");
					}
				}
            }
         });
    });


