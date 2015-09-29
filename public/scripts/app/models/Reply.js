define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/reply/',
        defaults: {
            id: 0,
            ask_id: 0,
            type: 2,
            is_download: false,
            uped: false,
            collected: false,
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20150326-1451205513ac68292ea.jpg",
            sex: 1,
            uid: 0,
            nickname: "",
            upload_id: 0,
            create_time: "",
            update_time: "",
            desc: "",
            up_count: 0,
            collect_count: 0,
            comment_count: 0,
            click_count: 0,
            inform_count: 0,
            share_count: 0,
            weixin_share_count: 0,
            image_width: 0,
            image_height: 0,
            image_url: ""
        },
        construct: function() {

        }
    });

}); 
