define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/comment/',
        defaults: {
            uid: 1,
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20151118-205001564c73f9ca9be.png",
            sex: 1,
            reply_to: 0,
            for_comment: 0,
            comment_id: 0,
            nickname: '',
            content: '123',
            up_count: 0,
            down_count: 0,
            inform_count:0,
            create_time: ' ',
            at_comment: [ ],
            target_id: 1,
            target_type: 1,
            uped: false
        },
        construct: function() {
        }
    });

}); 
