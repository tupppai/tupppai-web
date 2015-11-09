define(['app/collections/Base', 'app/models/Friendship'], function(Collection, Friendship) {
    return Collection.extend({
        model: Friendship,
        url: '/follows'
     });
}); 
