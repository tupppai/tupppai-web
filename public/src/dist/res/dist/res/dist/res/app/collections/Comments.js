define(['app/collections/Base', 'app/models/Comment'], function(Collection, Comment) {
    return Collection.extend({
        model: Comment,
        url: '/comments',
        initialize: function() {
            this.data = {
                type: 1,
                target_id: 1,
                page: 0,
                size: 10
            }
        }
     });
}); 
