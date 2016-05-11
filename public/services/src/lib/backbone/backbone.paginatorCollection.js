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
         
        data: {
            page: 1,
            size: 15
        },
        initialize: function() {
            this.data = {
                page: 1,
                size: 15
            }    
        },
        load_more: function(options) {
            var self = this; 
            self.data.page++;
            
            self.url = self.url + '?page=' + self.data.page;
            
            options = options ? _.clone(options) : {};
            options.success = function(data) {
                if (data.length > 0) {
                    _.each(data, function(item) {
                        self.add(item);    
                    });
                }

                if (data.length < self.data.size) {
                    self.finished();
                    console.log('none');    
                }
                if (data.length == self.data.size) {
                    self.not_finished();
                    console.log('normal');    
                }

                self.trigger('change');
            }
           
            this.sync('read', this, options);
        }
    }); 
}));
