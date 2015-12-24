define(['app/collections/Base', 'app/models/User'], function(Collection, User) {
    return Collection.extend({
        model: User,
        url: '/search'
     });
}); 
