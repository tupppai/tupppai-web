define([
        'app/views/Base', 
        'tpl!app/templates/homepage/HomeTask.html'
       ],
    function (View,template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .download" : "downloada",
                "click .reply-uploading-popup" : "askImageUrl",
                'click .refuse-uploading-popup': 'askiwq'
            },

            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
                var inProgressPopup = $(".inprogress-popup");
                    $(".refuse-uploading-popup").fancybox({
                        
                    }); 
                    $(".inprogress-popup").fancybox({
                            afterShow: function(){}
                    });
            },
            onShow:function() {
                $('.submit-rejecxt').click(function(){
                    var item = $('.check-item input:checked').val();
                    var items = $('.order-input').val();
                    var id = $('body').attr('data-id');
                    $.post('/task/refuse/'+id,{
                        reason_type: item,
                        refuse_reason:items,
                    },function(){
                        $.fancybox.close();
                    })
                })
            },
            askiwq:function(e){
                var id = $(e.currentTarget).attr('data-id');
                $('body').attr('data-id',id);
            },
           askImageUrl:function(e) {   
            debugger;    
                $('body').attr('data-task','task');
                var id = $(e.currentTarget).attr('data-id');
                $('body').attr('data-id',id);
                var ask_id = $(e.currentTarget).attr('ask-id');
                var categorty_id = $(e.currentTarget).find(".categorty-id").attr("data-id");
                $('#reply-uploading-popup').attr('ask-id', ask_id);
                $('#reply-uploading-popup').attr('data-id', categorty_id);
                var askImageUrl = $(e.currentTarget).parents('.conduct-right').siblings(".conduct-pic").find('img').attr('src');

                $('#ask_image img').attr('src', askImageUrl);
            }
        });
    });
