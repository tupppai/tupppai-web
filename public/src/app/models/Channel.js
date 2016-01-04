define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/channel/',
        defaults: {
            id: '',
            ask_id: '',
            type: 1,
            is_follow: true,
            is_fan: false,
            is_download: false,
            uped: false,
            collected: false,
            avatar: '',
            sex: 0,
            uid: '',
            nickname: '',
            upload_id: '',
            love_count: '',
            create_time: '',
            update_time: '',
            desc: '',
            up_count: '',
            comment_count: '',
            reply_count: '',
            click_count: '',
            inform_count: '',
            collect_count: '0',
            share_count: '0',
            weixin_share_count: 0,
            ask_uploads:[],
            image_url: '',
            image_width: 0,
            image_height: 0,
            image_ratio: '1.29',
            replies:[],
            users: [],
            isstar: ''
        }
    });

}); 
