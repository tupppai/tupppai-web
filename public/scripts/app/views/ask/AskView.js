define([
        'masonry', 
        'imagesLoaded',
        'app/views/Base',
        'app/models/Base', 
        'app/collections/Asks', 
        'tpl!app/templates/ask/AsksItemView.html'
       ],
    function (masonry, imagesLoaded, View, ModelBase, Asks, template) {

        "use strict";
        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'ask-container grid',
            template: template,
            events: {
                "click .download" : "downloadClick",
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                self.scroll();
                self.collection.loadMore();
       
            },
            render: function() {
                var template = this.template;
                var el = this.el;

                this.collection.each(function(model){
                    append(el, template(model.toJSON()));
                });

                this.onRender(); 
            },
             downloadClick: function(e) {
                var data = $(e.currentTarget).attr("data");
                var id   = $(e.currentTarget).attr("data-id");

                var model = new ModelBase;
                model.url = '/record?type='+data+'&target='+id;
                model.fetch({
                    success: function(data) {
                        var urls = data.get('url');
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                        });

                    }
                });
            },
            onRender: function() {
                // $('.ask-main').hover(function(){
                //     $(this + ' .person-message-2').animate(
                //         { opacity: 1 },400);
                // },function(){
                //     $(this).animate({opacity:"1"});
                // });

                var imgLoad = imagesLoaded('.is-loading', function() { 
                    //console.log('all image loaded');
                });
                imgLoad.on('progress', function ( imgLoad, image ) {
                    if(image.isLoaded) {
                        setTimeout(function() {
                            if(image) {
                                image.img.parentNode.className =  '';
                                $(image.img).css('opacity', 0);
                                //$(image.img).fadeIn(300);
                                $(image.img).animate({
                                    opacity: 1
                                }, 300, function() {
                                    var msnry = new masonry('.grid', {});    
                                });
                            }
                        }, 400);
                    }
                });
            }
        });
    });
