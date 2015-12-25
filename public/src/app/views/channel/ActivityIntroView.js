 define([ 
        'app/views/Base',
        'tpl!app/templates/channel/ActivityIntroView.html'
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: 'channel-reply-container',
            template: template,
            events: {
                "click .us-participation" : "participation"
            },
            participation:function(e) {
                var id = $(e.currentTarget).attr("data-id");
                var ask_id = $(e.currentTarget).attr("data-ask-id");

                 $.get('/record?target=' + ask_id +'&category_id='+ id +'&type=1', function( returnData ){
                        var ret = returnData.ret;
                    if(returnData.ret == 1) {
                        toast("参与成功,请在个人页面进行中上传作品");
                    } else {
                        toast("请先登录才能参与活动");
                    }
                });
            },
            construct: function() {
                this.listenTo(this.model, 'change', this.render);
            },            
        });
    });
