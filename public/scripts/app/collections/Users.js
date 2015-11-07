define(['app/collections/Base', 'app/models/User'], function(Collection, User) {
    return Collection.extend({
        model: User,
        url: '/search',
        flag: false,
        data: {
            type: 'normal',
            page: 1,
            size: 10,
        	keyword:''
        },
        initialize: function() {
        	this.fetch({
        		data: this.data,
        		success:function(a,b,c){
        			// console.log(b);
        		}
        	});
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
