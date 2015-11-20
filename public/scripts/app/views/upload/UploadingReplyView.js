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

                if( !upload_id ) {
                    error('上传作品','请上传作品');
                    return false;
                }
                $.post('replies/save', {
                    ask_id: ask_id,
                    upload_id: upload_id,
                    desc: desc
                }, function(data) {
                    $.fancybox.close();
                    location.href = '/#hotflows';
                    toast('上传成功',function(){
                        // location.reload();
                    });
                });

                $("#upload_picture-reply").attr("upload-id", '');
                $("#reply-uploading-popup").attr("ask-id", '');
                $(".upload-accomplish").parent().parent().find(".reply-content").val('');
            }
        });
    });
