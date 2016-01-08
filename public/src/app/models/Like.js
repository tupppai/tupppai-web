define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/like/',
        defaults: {
            id: 0,
            type: 0,
            status: 0,
            is_star: ''
        },
        construct: function() {

        },
        save: function(callback){ 
            this.fetch({
                data: this.toJSON(),
                success: function(data) {
                    callback && callback(data);
                }
            });
        }
    });

}); 
