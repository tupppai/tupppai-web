define([
        'app/views/home/HomeView', 
        'imagesLoaded',
        'app/collections/Replies', 
        'tpl!app/templates/home/ReplyItemView.html',
       ],
    function (View, imagesLoaded, Replies, ReplyItemTemplate) {
        "use strict";

        var replies = new Replies;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            collection: replies,
            template: ReplyItemTemplate,
            onRender: function() {
                $('#load_reply').addClass('designate-nav').siblings().removeClass('designate-nav');
                var imgLoad = imagesLoaded('.is-loading', function() { 
                    //console.log('all image loaded');
                });
                imgLoad.on('progress', function ( imgLoad, image ) {
                    if(image.isLoaded) {
                        //console.log('image loaded');
                        image.img.parentNode.className =  '';
                    }
                });
            },
   
        });
    });
