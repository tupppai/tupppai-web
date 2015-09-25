define(['backbone', 'underscore'], function(Backbone, _) {
    return Backbone.Collection.extend({
        parse: function(resp, xhr) {  
            //todo: error response
            console.log('parsing base collection');

            return resp.data;
        }
     });
}); 
