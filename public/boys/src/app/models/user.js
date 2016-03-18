define(['app/models/base'], function(Model) {
    return Model.extend({
        url: '/user',
        defaults: {
        	id: 1,
        	test: 1,
        	today_amount: 1,
        	request:'',
        }
	});
}); 
