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
                "click .reset-btn" : "uploadDesc"
            },
            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
                
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
                    toast('修改内容成功',function(){});

                });
            },
        });
    });
