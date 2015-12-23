define(['app/collections/Base', 'app/models/Inprogress'], function(Collection, Inprogress) {
    return Collection.extend({
        model: Inprogress,
        url: '/inprogresses'
     });
}); 
