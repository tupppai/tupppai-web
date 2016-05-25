define([
        'app/views/Base', 
        'app/collections/Inprogresses', 
        'app/views/homepage/HomeTask',
        'app/views/homepage/HomeComplete',
        'tpl!app/templates/homepage/HomeCollectionView.html'
       ],
    function (View, Inprogresses, HomeTask,HomeComplete,  template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collections: Inprogresses,
            events: {
                "click .download" : "download",
                "click .reply-uploading-popup" : "askImageUrl",
                "click .nav-item": 'task'
            },
            task:function(e) {
                $('.fans-nav').addClass("hide");
                $('.attention-nav').addClass("hide");
                $('.home-like-nav').addClass("hide");

                $('.nav-item').removeClass('active');
                $(e.currentTarget).addClass('active');
                    var uid = $(".menu-nav-reply").attr("data-id");
                var type = $(e.currentTarget).attr('data-type');
                $('#taskList').empty();
                if(type == 'task'){

                    var inprogress = new Inprogresses;
                    inprogress.url = '/task/doing'
                    var conductCantainer = new Backbone.Marionette.Region({el:"#taskList"});
                    var conduct_view = new HomeTask({
                        collection: inprogress
                    });
                    conduct_view.scroll();
                    conduct_view.collection.reset();
                    conduct_view.collection.data.uid = uid;
                    conduct_view.collection.data.page = 0;
                    conduct_view.collection.loading(this.showEmptyView);
                    conductCantainer.show(conduct_view);
                } else {

                    var inprogress = new Inprogresses;
                    inprogress.url = '/task/finished'
                    var conductCantainer = new Backbone.Marionette.Region({el:"#taskList"});
                    var conduct_view = new HomeComplete({
                        collection: inprogress
                    });
                    conduct_view.scroll();
                    conduct_view.collection.reset();
                    conduct_view.collection.data.uid = uid;
                    conduct_view.collection.data.page = 0;
                    conduct_view.collection.loading(this.showEmptyView);
                    conductCantainer.show(conduct_view);

                }
            },
            onShow:function() {
                $('li.nav-item[data-type=task]').trigger('click',function(){});

            },
            construct: function() {
                // this.listenTo(this.collection, 'change', this.render);

                var inProgressPopup = $(".inprogress-popup");
                    $(".inprogress-popup").fancybox({
                         afterShow: function(){
                            $('.conduct-upload').unbind('click').bind('click', askImageUrl);
                         }
                    }); 
            },
           askImageUrl:function(e) {   
                var ask_id = $(e.currentTarget).attr('ask-id');
                $('#reply-uploading-popup').attr('ask-id', ask_id);
                var askImageUrl = $(e.currentTarget).parents('.conduct-right').siblings(".conduct-pic").find('img').attr('src');

                $('#ask_image img').attr('src', askImageUrl);
            }
        });
    });
