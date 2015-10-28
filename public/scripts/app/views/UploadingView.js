define([ 'common', 'uploadify','app/views/Base', 'tpl!app/templates/UploadingView.html'],
    function (Conmmon, uploadify, View, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this; 
                $(".uploading-popup").fancybox({ });
            },
            onRender:function() {
                var self = this;

                Common.upload("#upload_picture", function(data){
                    $("#uploading-popup input[name='show-picture']").val(data.data.url);
                    $("#uploading-popup .show-picture").attr("src", data.data.url);
                    $('.upload-middle').addClass('opacity'); 
                    $('.show-picture').removeClass('opacity');                  

                    $("#upload_picture").attr("upload-id", data.data.id);

                    $(".upload-accomplish").unbind('click').bind('click', self.upload).removeClass('disable');
                }, null, {
                     url: '/upload'
                }); 
            },
            upload: function() {
                var upload_id = $("#upload_picture").attr("upload-id");
                var ask_id    = $("#upload_picture").attr("ask-id");
                
                var desc = $(".upload-accomplish").parent().parent().find(".reply-content").val();

                if(!upload_id) {
                    alert('请上传作品');
                    return false;
                }

                $.post('replies/save', {
                    ask_id: ask_id,
                    upload_id: upload_id,
                    desc: desc
                }, function(data) {
                    $.fancybox.close();
                    location.href = '#hots';
                    location.reload();
                });
            }
        });
    });
