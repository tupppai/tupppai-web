define(['backbone', 'underscore'], function(Backbone, _) {
    return Backbone.Collection.extend({
        parse: function(resp, xhr) {  
            //todo: error response
            //console.log('parsing base collection');
            if(resp.ret == 0 && resp.code == 1 && this.url != 'user/status'){
                $(".login-popup").click();
            }
            else if(resp.ret == 0 && this.url != 'user/status') {
                error('操作失败', resp.log);
            }
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
