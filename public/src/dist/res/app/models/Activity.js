define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/activity/',
        defaults: {
            id: 10,
            display_name: "",
            pc_pic: "",
            pc_banner_pic: "",
            url: "",
            pid: 4,
            icon: "",
            post_btn: "",
            description: "",
            download_count: 0,
            click_count: 0,
            replies_count: 0,
            ask_id: 0,
            users: [ ]
        }
    });

}); 
