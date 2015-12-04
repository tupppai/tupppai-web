define([
        'app/views/Base', 
        'app/collections/Asks',
        'tpl!app/templates/homepage/HomeAskView.html'
       ],
    function (View, Asks, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            collections: Asks,
            events: {
                "click .reset-icon" : "uploadDesc",
            },
            uploadDesc: function(e) {
                var upload_id = $(e.currentTarget).attr("data-id");
                var desc = $(e.currentTarget).siblings(".desc").val();
      
                $.post('asks/save', {
                    upload_id: upload_id,
                    desc: desc
                }, function(data) {
                    toast('修改求P内容成功',function(){});

                });
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
                self.collection.loading();
            },
            
        });
    });
