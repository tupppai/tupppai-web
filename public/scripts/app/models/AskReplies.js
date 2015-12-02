define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/ask/',
        defaults: {
            ask: {
                id: 0,
                ask_id: 0,
                uped: false,
                type: 1,
                up_count: 0,
                comment_count: 0,
                click_count: 0,
                inform_count: 0,
                share_count: 0,
                weixin_share_count: 0,
                reply_count: 0,
                collected: false,
                desc: '',
                image_url: '',
                image_width: '',
                image_height: '',
                image_ratio: '',
                ask_uploads: [],
                avatar: 'http://7u2spr.com1.z0.glb.clouddn.com/20151118-205001564c73f9ca9be.png',
                uid: '',
                username: '',
                nickname: '',
                create_time: '',
                comments: [],
            },
            replies: []
        }
    });

}); 
