define(['backbone', 'underscore'], function(Backbone, _) {
    return Backbone.Collection.extend({
        parse: function(resp, xhr) {  
            //todo: error response
            //console.log('parsing base collection');
            $('.download-action').click(function(){
                if( resp.ret == 0 ) {
                    alert( '请先登录账号' );
                }
            })
            $('#comment-btn').click(function(){
                if( resp.ret == 0 ) {
                    alert( '请先登录账号' );
                }
            })
            return resp.data;
        },
        plock: false,
        lock: function(){ 
            this.plock = true;
        },
        unlock: function() {
            this.plock = false;
        }
     });
}); 
