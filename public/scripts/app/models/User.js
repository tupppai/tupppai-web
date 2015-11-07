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
<<<<<<< HEAD
            sex: 1,
=======
            sex: 0,
>>>>>>> 26d80fb552330f23b662be59a98a8e24e4685330
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20150326-1451205513ac68292ea.jpg",
            uped_count: "0",
            current_score: 0,
            paid_score: 0,
            total_praise: 0,
            location: "",
            province: "",
            city: "",
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
            replies: [ ]
        },
        construct: function() {

        }

    });
}); 
