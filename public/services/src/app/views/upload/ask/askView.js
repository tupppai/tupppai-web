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
            },
            emptyPic: function(e) {
                $("#fileList").text("");
                $(".holderBorder").show();
                $(".holderMain").show();
                $(".uploadCancel").addClass("hide");
            },
            fnSubmitDynamic:function() {
                var channel_id = $("body").attr("channel_id")   //频道id
                                    
                // var upload_id = $("#append_image").find("img").attr("src");    //图片地址
                var upload_id = $("body").attr("upload_id");    
                var titleDynamic = $('.uploadDesc').val();
                var data = {
                    desc: titleDynamic,
                    upload_id: upload_id,
                    category_id: channel_id
                };

                if(titleDynamic.length >= 5) {
                    $.post('/asks/save',data,function(rData){
                        fntoast('发布成功','hide');
                        if(channel_id) {
                            redirect('#channel/detail/' + channel_id);
                        } else {
                            redirect('#original/index');
                        }
                    })
                } else {
                    fntoast('请输入至少五个字的描述','hide');
                }

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
                        $(".uploadText").addClass("hide");
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