define([
        'app/views/home/HomeView', 
        'imagesLoaded',
        'app/models/Base', 
        'app/collections/Asks', 
        'tpl!app/templates/home/AskItemView.html'
       ],
    function (View, imagesLoaded, ModelBase, Asks, askItemTemplate) {
        "use strict";

        var asks = new Asks;

        return View.extend({
            tagName: 'div',
            className: 'photo-container',
            data: 0,
            collection: asks,
            template: askItemTemplate,
            onRender: function() {
                $('.download').unbind('click').bind('click',this.download);
                $('#load_ask').addClass('designate-nav').siblings().removeClass('designate-nav');

                this.loadImage();
            }
        });
    });
