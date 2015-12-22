define(['app/collections/Base', 'app/models/Topic'], function(Collection, Topic) {
    return Collection.extend({
        model: Topic,
        url: '/topics'
     });
}); 
