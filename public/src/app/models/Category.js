define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/category/',
        defaults: {
            id: "",
            display_name: "",
            pc_pic: "",
            pc_banner_pic: "",
            url: "",
            pid: "",
            icon: "",
            post_btn: "",
            description: "",
            category_type: "",
            isstar: ''
        }
    });

}); 
