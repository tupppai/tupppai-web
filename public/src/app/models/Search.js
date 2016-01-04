define(['app/models/Base'], function(Model) {
    return Model.extend({
        defaults: {
            type: ' ',
            keyword: ' ',
            isstar: ''
        },
        construct: function() {

        }
    });

}); 
