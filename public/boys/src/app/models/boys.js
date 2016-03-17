define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/WXActGod/index',
        defaults: {
			category: {
			id: "",
			name: "",
			display_name: "",
			description: "",
			click_count: "",
			uped_count: "",
			icon: "",
			post_btn: "",
			pid: "",
			status: "",
			order: "",
			create_by: "",
			create_time: "",
			update_by: "",
			update_time: "",
			start_time: "",
			end_time: "",
			pc_pic: "",
			app_pic: "",
			banner_pic: "",
			pc_banner_pic: "",
			url: ""
			},
			today_amount: 1,
			left_amount: 1,
			total_amount: 1
        }
    });
}); 
