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
            if(resp.ret == 0 && resp.code == 1 && this.url != 'user/status'){
                $(".login-popup").click();
                return false;
            }
            //console.log('parsing base modelxxx');
            return resp.data;
        }
    });

}); 
