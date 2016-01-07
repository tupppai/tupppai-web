define(['app/collections/Base', 'app/models/Message'], function(Collection, Message) {
    return Collection.extend({
        model: Message,
        url: '/messages',
        initialize: function() {
            this.data = {
                type: 'normal',
                page: 0,
                size: 10
            }
        }
     });
}); 
