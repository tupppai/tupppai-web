define(['backbone'], function(Backbone) {
    return Backbone.Model.extend({
        defaults: {
        },
        data: {
        },
        initialize: function(data){
            if(data) for(var i in this.defaults) {
                this.set(i, data[i]); 
            }
            this.construct(data);
        },
        construct: function(data) {
        },
        parse: function(resp, xhr) {
            //todo: error response
            $('.download-action').click(function(){
                if( resp.ret == 0 ) {
                    alert( '请先登录账号' );
                    return false;
                }
            })
            console.log('parsing base modelxxx');
            return resp.data;
        }
    });

}); 
