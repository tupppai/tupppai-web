define(['app/models/base'], function(Model) {
    return Model.extend({
        url: '/wxactgod/avatars',
        defaults: {
        	avatars:[],
        }
	});
}); 
