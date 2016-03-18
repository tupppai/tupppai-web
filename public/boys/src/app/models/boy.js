define(['app/models/base'], function(Model) {
    return Model.extend({
        url: '/user',
        defaults: {
 			code: 1,
 			data: {
 				total_amount: 44,
				left_amount: 6,
				rand: 0,
				reason: null,
				designer_name: "jq",
				avatars:[]
 			}
        }
	});
}); 
