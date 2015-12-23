define(['app/collections/Base', 'app/models/Thread'], function(Collection, Reply) {
    return Collection.extend({
        model: Reply,
        url: '/threads'
     });
}); 
