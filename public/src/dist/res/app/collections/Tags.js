define(['app/collections/Base', 'app/models/Tag'], function(Collection, Tag) {
    return Collection.extend({
        model: Tag,
        url: '/tags'
     });
}); 
