define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/ask/',
        defaults: {
            id: 0,
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
            ask_uploads: [],
            avatar: 'http://7u2spr.com1.z0.glb.clouddn.com/20150326-1451205513ac68292ea.jpg',
            uid: '',
            username: '',
            nickname: '',
            create_time: '',
            comments: [],
            replyers: []

        },
        construct: function() {

        }
    });

}); 
