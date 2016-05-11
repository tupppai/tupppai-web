define(['tpl!app/views/upload/ask/ask.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            onShow: function() {
                title('发布求P');
                $(".menuPs").addClass("hide");
            },
            events: {
                "click .uploadCancel": "emptyPic",
                "click .confirm": "fnSubmitDynamic",
                "click #uploadWork": "fnUploadImage",
                "keydown .uploadDesc": "uploadDesc",
            },
            emptyPic: function(e) {
                $("#fileList").text("");
                $(".holderBorder").show();
                $(".holderMain").show();
                $(".uploadCancel").addClass("hide");
            },
            uploadDesc: function(e) {
                var upload_id = $("body").attr("upload_id");
                var titleDynamic = $('.uploadDesc').val();
                if(titleDynamic.length >= 10 && upload_id) {
                    $(".confirm-none").addClass("confirm");
                }
            },
            fnSubmitDynamic:function() {
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
                    desc: titleDynamic,
                    upload_id: upload_id
                }
                if(titleDynamic == '') {
                    fntoast('内容不能为空','hide')
                    return false
                }
                if(!imgs) {
                    fntoast('图片不能为空','hide')
                    return false
                }
                $.post('/asks/save',data,function(rData){
                    fntoast('发布成功','hide');
                    setTimeout(function(){
                    },1500)
                    location.href = '#ask/index';
                })
            },
            fnUploadImage:function() {
                 wx.chooseImage({
                     count: 1, // 默认9
                     success: function (res) {
                         var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                         var localId = localIds.toString();
                         var titleDynamic = $('.uploadDesc').val();
                         var imageTpl = '<div class="clips-wrapper"><img src="'+localId+'" class="clips"></div>';
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
                                    if(titleDynamic.length >= 10) {
                                        $(".confirm-none").addClass("confirm");
                                    }
                                })
                            }
                        });
                     }
                 });
            }
            // handleFiles:function(e) {
            //     window.URL = window.URL || window.webkitURL;
            //     var fileElem = document.getElementById("fileElem"),
            //         fileList = document.getElementById("fileList");

            //     var files = e.target.files,
            //         img = new Image();
            //     if(window.URL){
            //         //File API
            //           img.src = window.URL.createObjectURL(files[0]); //创建一个object URL，并不是你的本地路径
            //           img.onload = function(e) {
            //              window.URL.revokeObjectURL(this.src); //图片加载后，释放object URL
            //           }
            //           $("#fileList").text("");
            //           fileList.appendChild(img);
            //           $(".holderBorder").hide();
            //           $(".holderMain").hide();
            //           $(".uploadCancel").removeClass("hide");
            //     }else if(window.FileReader){
            //         //opera不支持createObjectURL/revokeObjectURL方法。我们用FileReader对象来处理
            //         var reader = new FileReader();
            //         reader.readAsDataURL(files[0]);
            //         reader.onload = function(e){
            //             img.src = this.result;
            //             img.width = 200;
            //             $("#fileList").text("");
            //             fileList.appendChild(img);
            //             $(".holderBorder").hide();
            //             $(".holderMain").hide();
            //             $(".uploadCancel").removeClass("hide");
            //         }
            //     }
            // }
        });
    });


