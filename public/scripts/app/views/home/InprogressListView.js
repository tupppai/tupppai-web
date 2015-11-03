define([ 
        'app/views/home/HomeView', 
        'imagesLoaded',
        'app/collections/Inprogresses', 
        'tpl!app/templates/home/InprogressItemView.html',
       ],
    function (View, imagesLoaded, Inprogresses, InprogressItemTemplate ) {
        "use strict";

        var inprogresses = new Inprogresses;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: inprogresses,
            template: InprogressItemTemplate,
     
            onRender: function() {
                $('#load_inprogress').addClass('designate-nav').siblings().removeClass('designate-nav');

                $("#uploading-popup .upload-header").text('上传作品');
                $(".uploading-popup").unbind('click').bind('click', function(){
                    var id = $(this).attr("ask-id");
                    $("#upload_picture").attr("ask-id", id);
                }); 
                
                this.loadImage();
            },
        
        });
    });
