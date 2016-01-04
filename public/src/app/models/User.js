define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/users',
        data: {
            uid: 0
        },
        defaults: {
            uid: "",
            username: "",
            nickname: "",
            phone: "",
            sex: 0,
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20151118-205001564c73f9ca9be.png",
            uped_count: "0",
            current_score: 0,
            paid_score: 0,
            total_praise: 0,
            location: "",
            province: "",
            city: "",
            badges_count: "",
            bg_image: null,
            status: 1,
            is_bound_weixin: 0,
            is_bound_qq: 0,
            is_bound_weibo: 0,
            weixin: "",
            weibo: "",
            qq: "",
            fans_count: 0,
            fellow_count: 0,
            ask_count: 0,
            reply_count: 0,
            inprogress_count: 0,
            collection_count: 0,
            is_follow: 0,
            is_fan: 0,
            has_invited: false,
            replies: [ ],
            isstar: ''
        },
        construct: function() {

        }

    });
}); 
