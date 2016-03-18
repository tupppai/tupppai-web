define(['app/models/base'], function(Model) {
    return Model.extend({
        url: '/user',
        defaults: {
 			code: 1,
 			data: {
				total_amount:'',
				left_amount:'',
				image:''
 			},
 			result:{
				reason: "",
 			}
        }
	});
}); 
