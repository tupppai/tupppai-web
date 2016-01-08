define(['app/models/Base'], function(Model) {
    return Model.extend({
        defaults: {
            type: ' ',
            keyword: ' ',
            is_star: ''
        },
        construct: function() {

        }
    });

}); 
