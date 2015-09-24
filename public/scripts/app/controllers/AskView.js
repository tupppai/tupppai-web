define(['app/collections/Base', 'app/models/Ask'], function(Collection, Ask) {
    return Collection.extend({
        url: '/ask/getAsksByType',
        model: Ask,
        initialize: function() {
            console.log('fetching asks');
        },
     });
}); 
