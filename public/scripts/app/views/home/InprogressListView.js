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

                
                this.loadImage();
            },
        
        });
    });
