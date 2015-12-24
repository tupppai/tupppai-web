define([
        'underscore',
        'app/views/Base',
        'app/collections/Inprogresses',
        'tpl!app/templates/upload/InprogressItemView.html'
       ],
    function (_, View, Inprogresses, template) {

        var InprogressItemView = '#InprogressItemView';

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collection: Inprogresses,

            construct: function() { 
                var self = this;
                var category_id = $(".login-upload").attr("data-id");
                self.collection.category_id = category_id;
                self.listenTo(self.collection, 'change', self.render);
                self.collection.loading(self.showEmptyView);
            },
            onRender:function() {
                setTimeout(function(){
                    $('.reply-uploading-popup').click(function(e){
                        var ask_id = $(e.currentTarget).attr('ask-id');
                        $('#reply-uploading-popup').attr('ask-id', ask_id);
                        var askImageUrl = $(e.currentTarget).parent().siblings('.ask-image').find('img').attr('src');
                        $('#ask_image img').attr('src', askImageUrl);
                    });
                },2000)
            },
            render: function() {
                var template = this.template;

                this.collection.each(function(model){
                    var html = template(model.toJSON());
                    append(InprogressItemView, html);
                });
                this.onRender();
            },
        });
    });
