define(['app/collections/Base', 'app/models/Ask'], function(Collection, Ask) {
    return Collection.extend({
        model: Ask,
        url: '/asks'
     });
}); 
