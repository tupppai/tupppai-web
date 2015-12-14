define([
        'app/views/Base', 
        'app/collections/Asks',
        'tpl!app/templates/homepage/HomeAskView.html'
       ],
    function (View, Asks, template) {
        
        var Asks = new Asks;
        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collections: Asks,
            events: {
                "click .reset-btn" : "uploadDesc"
            },
            construct: function() {
                var uid = $(".menu-nav-reply").attr("data-id");
                var self = this;
                self.listenTo(self.collection, 'change', self.render);
                self.scroll();
                self.collection.reset();
                self.collection.data.uid = uid;
                self.collection.data.page = 0;
                self.collection.data.type = 'ask';
                self.collection.loading(self.showEmptyView);
            },
            showEmptyView: function(data) {
                if(data.data.page == 1 && data.length == 0) {
                    append($("#contentView"), ".emptyContentView");
                }
            },
            onRender: function() {
                var own_id = $(".homehead-cantainer").attr("data-id");
                var uid = window.app.user.get('uid');

                if( own_id == uid ) {
                    $('.edit_self').removeClass("hide");
                    $(".reset-icon").css({
                        display: "block"
                    })
                } else {
                    $('.edit_others').removeClass("hide");
                    display: "none"
                }
            },
            uploadDesc: function(e) {
                var id = $(e.currentTarget).attr("data-id");
                var desc = $(e.currentTarget).siblings(".desc").val();
      
                $.post('asks/save', {
                    id: id,
                    desc: desc
                }, function(data) {
                    toast('修改求P内容成功',function(){});

                });
            },
        });
    });
