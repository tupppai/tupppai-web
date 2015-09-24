define(['app/collections/Base', 'app/models/Ask'], function(Collection, Ask) {
    return Collection.extend({
        url: '/asks',
        flag: false,
        data: {
            page: 0,
            size: 10
        },
        //model: Ask,
        initialize: function() {
            console.log('fetching asks');
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
                    callback && callback(data);
                }
            });
        }
     });
}); 
