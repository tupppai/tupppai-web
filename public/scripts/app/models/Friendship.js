define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/follows/',
        defaults: {
            uid: "84",
            username: "majia2991125071",
            nickname: "白汐",
            phone: "19000000084",
            sex: 0,
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20150417-12241755308af10e86c.jpg",
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
            fans_count: 3,
            fellow_count: 0,
            ask_count: 3,
            reply_count: 397,
            inprogress_count: 387,
            collection_count: 0,
            is_follow: 1,
            is_fan: 0,
            has_invited: false
        },
    });

}); 
