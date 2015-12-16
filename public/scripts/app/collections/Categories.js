define(['app/collections/Base', 'app/models/Category'], function(Collection, Category) {
    return Collection.extend({
        model: Category,
        url: '/categories'
     });
}); 
