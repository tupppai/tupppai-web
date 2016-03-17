define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/WXActGod/index',
        defaults: {
			category: {
				id: 1,
				name: "",
				display_name: "",
				description: "",
				click_count: 0,
				uped_count: 0,
				icon: "",
				post_btn: "",
				pid: 8,
				status: 1,
				order: 0,
				create_by: 1,
				create_time: 1,
				update_by: 1,
				update_time: 1,
				start_time: 0,
				end_time: 1,
				pc_pic: "",
				app_pic: "",
				banner_pic: "",
				pc_banner_pic: "",
				url: ""
	        },
	        today_amount: 1,
			left_amount: 0,
			total_amount: 6,
			avatars:[]
	    }
	});
}); 
