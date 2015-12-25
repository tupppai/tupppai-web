define([ 'common', 'uploadify','app/views/Base'],
    function (Conmmon, uploadify, View) {
        "use strict";
        
        return View.extend({
            construct: function () {
                var self = this; 
                $(".reply-uploading-popup").fancybox({ 
                     afterShow: function(){
                        
                     }
                });

                Common.upload("#upload_picture-reply", function(data){
                    $("#reply-uploading-popup input[name='show-picture']").val(data.data.url);
                    $("#reply-uploading-popup .show-picture").attr("src", data.data.url);
                    $('.upload-middle').addClass('opacity'); 
                    $('.show-picture').removeClass('opacity');                  

                    $("#upload_picture-reply").attr("upload-id", data.data.id);

                    $(".upload-accomplish").removeClass('disable').unbind('click').bind('click', self.upload);
                }, null, {
                     url: '/upload'
                });
            },
            upload: function(e) {
                var upload_id = $("#upload_picture-reply").attr("upload-id");
                var ask_id    = $('#reply-uploading-popup').attr("ask-id");
                var desc      = $("#reply-uploading-popup .reply-content").val();
                var category_id = $('#reply-uploading-popup').attr("data-id");

                if( !desc) {
                    error('提示','内容不能为空');
                    return false;
                }
                if( !upload_id ) {
                    error('上传作品','请上传作品');
                    return false;
                }
                    $.post('replies/save', {
                        ask_id: ask_id,
                        upload_id: upload_id,
                        category_id: category_id,        
                        desc: desc,
                    }, function(data) {
                        $.fancybox.close();
                        history.go(-1);
                        location.reload();    
                            $('.title-bar').removeClass("hide");
                            $('.header-back').removeClass("height-reduce");
                            $(".reply-index").addClass("active").siblings().removeClass("active");
                             toast('上传成功',function(){
                            // location.reload();
                        });
                    });
                    var src = "http://7u2spr.com1.z0.glb.clouddn.com/20151205-154952566297205441e.png";
                $(".upload-middle").removeClass("opacity");
                $(".show-picture").attr("src", src);
                $("#upload_picture-reply").attr("upload-id", '');
                $("#reply-uploading-popup").attr("ask-id", '');
                $(".upload-accomplish").parent().parent().find(".reply-content").val('');
            }
        });
    });
