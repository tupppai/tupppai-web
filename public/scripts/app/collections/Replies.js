define(['app/collections/Base', 'app/models/Reply'], function(Collection, Reply) {
    return Collection.extend({
        model: Reply,
        url: '/replies',
        flag: false,
        data: {
            page: 0,
            size: 10
        },
        initialize: function() {
            console.log('fetching replies');
            this.data = {
                page: 0,
                size: 10
            }
            this.flag = false;
        },
        loadMore: function(callback) {
            if(this.flag) {
                return true;
            }
            var self = this;
            this.flag = true;

            this.data.page ++;
            this.fetch({
                data: this.data,
                success: function(data) {
                    self.flag = false;
                    self.trigger('change');
                    callback && callback(data);
                }
            });
        }
     });
}); 
