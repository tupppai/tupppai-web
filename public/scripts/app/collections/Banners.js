define(['app/collections/Base', 'app/models/Banner'], function(Collection, Banner) {
    return Collection.extend({
        model: Banner,
        url: '/banners',
        flag: false,
        data: {
            type: 'normal',
            page: 0,
            size: 10
        },
        initialize: function() {
            this.data = {
                type: 'normal',
                page: 0,
                size: 10
            }
            this.flag = false;
        },
        loadMore: function(callback) {
            var self = this;
            if( self.plock ) return true;
            self.lock();

            this.data.page ++;
            this.fetch({
                data: this.data,
                success: function(data) {
                    self.unlock();
                    self.trigger('change');
                    callback && callback(data);
                }
            });
        }
     });
}); 
