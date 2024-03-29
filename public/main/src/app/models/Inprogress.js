define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/inprogress/',
        defaults: {
            id: 0,
            ask_id: 0,
            type: 1,
            uped: false,
            up_count: 0,
            comment_count: 0,
            click_count: 0,
            grade_reason:0,
            inform_count: 0,
            balance:0,
            share_count: 0,
            weixin_share_count: 0,
            reply_count: 0,
            collected: false,
            desc: '',
            ask:'',
            image_width: 0,
            status_text:0,
            image_height: 0,
            image_url: '',
            ask_uploads: [],
            categories: [],
            avatar: '',
            uid: '',
            username: '',
            nickname: '',
            create_time: '',
            is_star: ''

        },
        construct: function() {

        }
    });

}); 
