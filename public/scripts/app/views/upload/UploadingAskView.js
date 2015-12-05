define([ 'common', 'uploadify','app/views/Base'],
    function (Conmmon, uploadify, View) {
        "use strict";
        
        return View.extend({
            construct: function () {
                var self = this; 
                $(".ask-uploading-popup").fancybox({ 
                    afterShow: function(){
                        $(".new-label span").unbind('click').bind('click', self.spanChange);
                    }

                });
                Common.upload("#upload_picture", function(data){
                    $("#ask-uploading-popup input[name='show-picture']").val(data.data.url);
                    $("#ask-uploading-popup .show-picture").attr("src", data.data.url);
                    $('.upload-middle').addClass('opacity'); 
                    $('.show-picture').removeClass('opacity');                  
                    $("#upload_picture").attr("upload-id", data.data.id);
                    $(".upload-accomplish").removeClass('disable').unbind('click').bind('click', self.upload);
                }, null, {
                     url: '/upload'
                });
            },
            spanChange: function(e) {
                $(e.currentTarget).toggleClass("new-change");
            },
            upload: function() {
                    alert(1);
                var upload_id = $("#upload_picture").attr("upload-id");
                var desc      = $("#ask-uploading-popup .ask-content").val();
                var status    = [];
                for(var i = 0; i < $(".new-label span").length; i++) {
                    if($(".new-label span").className == "new-change") {
                        status.push($(".new-label span").attr("id"))
                    }
                };
                console.log(status);
                if( !upload_id ) {
                    error('上传作品','请上传作品');
                    return false;
                }
     
                $.post('asks/save', {
                    upload_id: upload_id,
                    desc: desc,
                    status: status
                }, function(data) {
                    // $.fancybox.close();
                    location.href = '/#askflows';
                    toast('上传成功',function(){
                        location.reload();
                    });
                });
                $("#upload_picture").attr("upload-id", '');
                $(".upload-accomplish").parent().parent().find(".ask-content").val('');


            }
        });
    });
