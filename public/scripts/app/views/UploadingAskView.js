define([ 'common', 'uploadify','app/views/Base', 'tpl!app/templates/UploadingAskView.html'],
    function (Conmmon, uploadify, View, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this; 
                $(".uploading-header-popup").fancybox({ });
            },
            onRender:function() {
                var self = this;
                Common.upload("#upload_picture", function(data){
                    $("#uploading-popup input[name='show-picture']").val(data.data.url);
                    $("#uploading-popup .show-picture").attr("src", data.data.url);
                    $('.upload-middle').addClass('opacity'); 
                    $('.show-picture').removeClass('opacity');                  

                    $("#upload_picture").attr("upload-id", data.data.id);

                    $(".upload-accomplish").unbind('click').bind('click', self.upload);
                }, null, {
                     url: '/upload'
                });  
            },
            upload: function() {
                var upload_id = $("#upload_picture").attr("upload-id");
                var desc = $(".ask-content").val();
    
                $.post('/asks/save', {
                    upload_id: upload_id,
                    desc: desc
                }, function(data) {
                    //location.reload();
                });
            }
        });
    });
