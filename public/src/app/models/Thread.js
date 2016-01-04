define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/thread/',
        defaults: {
            id: ' ',
            ask_id: ' ',
            type: 1,
            is_follow: false,
            is_download: false,
            uped: false,
            collected: false,
            avatar: "http://7u2spr.com1.z0.glb.clouddn.com/20151028-16575056308e0eec2ce.jpg",
            sex: ' ',
            uid: ' ',
            nickname: ' ',
            upload_id: ' ',
            create_time: ' ',
            update_time: ' ',
            desc: ' ',
            up_count: ' ',
            comment_count: ' ',
            collect_count: 0,
            click_count: ' ',
            inform_count: 0,
            share_count: ' ',
            weixin_share_count: ' ',
            reply_count: 0,
            ask_uploads: [],
            image_url: "http://7u2spr.com1.z0.glb.clouddn.com/20151028-2003045630b9780ebca.jpg?imageView2/2/w/480",
            image_width: 480,
            image_height: 480,
            isstar: ''
        },
        construct: function() {

        }
    });

}); 
