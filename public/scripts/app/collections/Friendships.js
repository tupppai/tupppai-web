define(['app/collections/Base', 'app/models/Friendship'], function(Collection, Friendship) {
    return Collection.extend({
        model: Friendship,
        url: '/follows',
        flag: false,
        data: {
            page: 0,
            size: 10
        },
        initialize: function() {
            this.data = {
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
