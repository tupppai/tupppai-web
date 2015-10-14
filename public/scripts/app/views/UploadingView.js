define([ 'common', 'uploadify','app/views/Base', 'tpl!app/templates/UploadingView.html'],
    function (Conmmon, uploadify, View, template) {
        "use strict";
        
        return View.extend({
            template: template,
            construct: function () {
                var self = this;
                $(".uploading-popup").fancybox({

                });
            },
            onRender:function() {
                this.loadImage();

                Common.upload("#upload_picture", function(data){
                    $("#uploading-popup input[name='show-picture']").val(data.data.url);
                    $("#uploading-popup .show-picture").attr("src", data.data.url);
                    $("#uploading-popup .show-picture").parent().removeClass('.is-loading');
                }, null, {
                     url: '/upload'
                });
            }
        });
    });
