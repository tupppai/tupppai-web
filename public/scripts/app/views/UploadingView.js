define([ 'common', 'uploadify','app/views/Base'],
    function (Conmmon, uploadify, View) {
        "use strict";
        
        return View.extend({
            construct: function () {
                var self = this; 
                $(".uploading-popup").fancybox({ });

                Common.upload("#upload_picture", function(data){
                    $("#uploading-popup input[name='show-picture']").val(data.data.url);
                    $("#uploading-popup .show-picture").attr("src", data.data.url);
                    $('.upload-middle').addClass('opacity'); 
                    $('.show-picture').removeClass('opacity');                  

                    $("#upload_picture").attr("upload-id", data.data.id);

                    $(".upload-accomplish").removeClass('disable').unbind('click').bind('click', self.upload);
                }, null, {
                     url: '/upload'
                });
            },
            upload: function() {
                var upload_id = $("#upload_picture").attr("upload-id");
                var ask_id    = $("#upload_picture").attr("ask-id");
                var desc      = $("#uploading-popup .reply-content").val();

                if( !upload_id ) {
                    alert('请上传作品');
                    return false;
                }

                if(ask_id && ask_id != undefined) {
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
                else {
                    $.post('asks/save', {
                        upload_id: upload_id,
                        desc: desc
                    }, function(data) {
                        $.fancybox.close();
                        location.href = '#asks';
                        location.reload();
                    });
                }

                $("#upload_picture").attr("upload-id", '');
                $("#upload_picture").attr("ask-id", '');
                $(".upload-accomplish").parent().parent().find(".reply-content").val('');
            }
        });
    });
