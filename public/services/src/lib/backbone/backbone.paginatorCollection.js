/**
 * paginator collections
 */

(function(root, factory) {

    var Backbone = root.Backbone;

    if(typeof define === 'function' && define.amd) {
        return define(['backbone'], function(Backbone) {
           return factory(Backbone);
        });    
    }

    Backbone.paginatorCollection = factory(Backbone);

}(this, function(Backbone) {

    return Backbone.Collection.extend({
        
        can_loading: true,
        data: {
            page: 1,
            size: 15,
        },
        initialize: function() {
            this.data = {
                page: 1,
                size: 15
            }    
        },
        load_more: function(options) {
            var self = this; 
            
            if (self.can_loading) {
                self.can_loading = false;

                self.data.page++;
           
                if (self.url.indexOf("page") > -1) {
                    self.url = self.url.split("?")[0];    
                } 
                self.url = self.url + '?page=' + self.data.page;
                
                console.log(self.url);

                options = options ? _.clone(options) : {};
                options.success = function(data) {
                    if (data.length > 0) {
                        _.each(data, function(item) {
                            self.add(item);   
                            self.trigger('change');
                        });
                    }
                    
                    if (data.length < self.data.size) {
                        options.finished();
                    }
                    if (data.length == self.data.size) {
                        options.not_finished();
                        
                        self.can_loading = true;
                    }

                }
           
                this.sync('read', this, options);
            }            
        }
    }); 
}));
