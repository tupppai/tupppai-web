define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/tags/',
        defaults: {
            id: 0,
            name: ""
        }
    });

}); 
