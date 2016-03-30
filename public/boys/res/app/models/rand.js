define(['app/models/base'], function(Model) {
    return Model.extend({
        url: '/user',
        defaults: {
			rand: 0
        }
	});
}); 
