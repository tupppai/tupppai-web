define(['app/collections/Base', 'app/models/Activity'], function(Collection, activity) {
    return Collection.extend({
        model: activity,
        url: '/activities'
     });
}); 
