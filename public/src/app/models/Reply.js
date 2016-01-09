define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/reply/',
        defaults: {
            id: ' ',
            ask_id: ' ',
            reply_id: ' ',
            type: 1,
            is_follow: false,
            is_download: false,
            uped: false,
            collected: false,
            avatar: "",
            sex: ' ',
            uid: ' ',
            love_count: ' ',
            nickname: ' ',
            upload_id: ' ',
            create_time: ' ',
            update_time: ' ',
            desc: ' ',
            up_count: ' ',
            comment_count: ' ',
            image_ratio: ' ',
            collect_count: 0,
            click_count: ' ',
            inform_count: 0,
            share_count: ' ',
            weixin_share_count: ' ',
            reply_count: 0,
            ask_uploads: [],
            image_url: "",
            image_width: 480,
            image_height: 480,
            is_star: ''
        }
    });

}); 
