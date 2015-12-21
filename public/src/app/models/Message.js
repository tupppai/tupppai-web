define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/message/',
        defaults: {
            id: "",
            sender: "",
            update_time: "",
            target_type: "",
            target_id: "",
            target_ask_id: "",
            content: "",
            desc: "",
            pic_url: "http://7u2spr.com1.z0.glb.clouddn.com/20150410-15385455277e0e92ff7.jpg",
            nickname: "",
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20150403-153144551e41e02770e.jpg",
            sex: 1,
            reply_id: "",
            comment_id: "",
            ask_id: "",
            type: "",
            for_comment: ''
        },
        construct: function() {

        }
    });

}); 
