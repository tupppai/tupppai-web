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
        parse: parse 
    });

}); 
