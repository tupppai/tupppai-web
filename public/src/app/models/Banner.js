define(['app/models/Base'], function(Model) {
    return Model.extend({
        url: '/banners/',
        defaults: {
            id: "",
            uid: "",
            small_pic: "http://7u2spr.com1.z0.glb.clouddn.com/20151102-16242256371db69a84e.ico",
            large_pic: "http://7u2spr.com1.z0.glb.clouddn.com/20151102-16241856371db2a4b37.ico",
            url: "",
            pc_url: "",
            desc: "",
            create_time: "",
            update_time: "",
            isstar: ''
        }
    });

}); 
