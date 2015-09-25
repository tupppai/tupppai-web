define(['backbone'], function(Backbone) {
    return Backbone.Model.extend({
        defaults: {
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
            console.log('parsing base model');
            return resp.data;
        }
    });

}); 
