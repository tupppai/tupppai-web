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

                $(".uploading-popup").click(function(){
                    var id = $(this).attr("ask-id");
                    $("#upload_picture").attr("ask-id", id);
                });

                $(".upload-accomplish").click(function() {
                    var upload_id = $("#upload_picture").attr("upload-id");
                    var ask_id    = $("#upload_picture").attr("ask-id");

                    if(!upload_id) {
                        alert('请上传作品');
                        return false;
                    }

                    $.post('replies/save', {
                        ask_id: ask_id,
                        upload_id: upload_id
                    }, function(data) {
                        $.fancybox.close();
                    });
                });
                
                this.loadImage();
            },
        
        });
    });
