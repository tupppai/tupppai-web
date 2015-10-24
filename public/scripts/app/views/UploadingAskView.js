define([ 'common', 'uploadify','app/views/Base', 'tpl!app/templates/UploadingAskView.html'],
    function (Conmmon, uploadify, View, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this; 
                $(".uploading-popup").fancybox({ });
            },
            onRender:function() {
                Common.upload("#upload_picture", function(data){
                    $("#uploading-popup input[name='show-picture']").val(data.data.url);
                    $("#uploading-popup .show-picture").attr("src", data.data.url);
                    $('.upload-middle').addClass('opacity'); 
                    $('.show-picture').removeClass('opacity');                  

                    $("#upload_picture").attr("upload-id", data.data.id);
                }, null, {
                     url: '/upload'
                }); 
            }
        });
    });
