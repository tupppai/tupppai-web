define(['app/collections/Base', 'app/models/Comment'], function(Collection, Comment) {
    return Collection.extend({
        model: Comment,
        url: '/comments',
        flag: false,
        data: {
            type: 1,
            target_id: 1,
            page: 0,
            size: 10,
            comment_type: 'new'
        },
        initialize: function() {
            //console.log('fetching comment');
            this.data = {
                type: 1,
                target_id: 1,
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
                    callback && callback(data.new_comments);
                }
            });
        }
     });
}); 
