define(['app/collections/Base', 'app/models/User'], function(Collection, User) {
    return Collection.extend({
        url: '/users',
        initialize: function() {
            console.log('fetching users');

            this.fetch();
        }
     });
}); 
