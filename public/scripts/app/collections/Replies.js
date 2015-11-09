define(['app/collections/Base', 'app/models/Reply'], function(Collection, Reply) {
    return Collection.extend({
        model: Reply,
        url: '/replies'
     });
}); 
