define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/comment/',
        defaults: {
            uid: "577",
            avatar: "http://wx.qlogo.cn/mmopen/ajNVdqHZLLDgTN9GKCUKCzDqZGD6J8pD9ks39HHzGdlia2UeLw1UT7hk5jzwTZmyic3ic7wsxdvzn1ic14UFCk2B4BgVYssqibvm6PpqUPRGX54I/0",
            sex: "1",
            reply_to: "4",
            for_comment: "0",
            comment_id: "183",
            nickname: "peiwei",
            content: "ef",
            up_count: "0",
            down_count: "0",
            inform_count: "0",
            create_time: "1443845097",
            at_comment: [ ],
            target_id: "1",
            target_type: "1",
            uped: false
        },
        construct: function() {

        }
    });

}); 
