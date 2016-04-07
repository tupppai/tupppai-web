define(['app/collections/base', 'app/models/ask'], function(Collection, Ask) {
    return Collection.extend({
        model: Ask,
        url: '/asks'
     });
}); 
