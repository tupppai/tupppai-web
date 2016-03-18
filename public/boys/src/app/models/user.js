define(['app/models/base'], function(Model) {
    return Model.extend({
        url: '/user',
        defaults: {
 			code: 1,
 			data: {
				result: {
					oper_by: "1",
					oper_time: 1458292687,
					assign_user: null,
					assign_status: "reject",
					reason: "就是太帅了",
					username: "billqiang",
					nickname: "jq",
				},
 				desc: "",
 			},
        }
	});
}); 
