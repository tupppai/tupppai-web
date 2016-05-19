define(['app/views/activity/list/index', 'app/views/activity/header/headerView'], 
	function (list, headerView) {
    "use strict";
    return function() {
        var sections = ['_header', '_content'];
        var layoutView = window.app.render(sections);

        // var collection = new window.app.collection();
        // collection.url= "/v2/populars";
        var header = new headerView({
            // collection: collection
        });
        window.app.show(layoutView._header, header);         

        var collection = new window.app.collection();
        collection.url= "/activities?page=1&size=15&activity_id=1010&type=hot";
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView._content, lv); 

        lv.on('show', function() {
            this.$el.asynclist({
                root: this,
                collection: this.collection,
                renderMasonry: true,
                itemSelector: 'loading' 
            });
            $(".menuPs").addClass("hide");
            
            var img = $(".sectionContent").eq(0).find("img").attr("src");
            var title = $(".activity-title").text();
            //电影详情页面微信分享文案
            var options = {};
            options.title    = title;
            options.desc    = "晒创意毕业照赢宋仲基粉丝见面会门票啦";
            options.img    = "http://7u2spr.com1.z0.glb.clouddn.com/20160519-170929573d82c99d903.jpeg?imageView2/2/w/480";
            
            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){});
        });    

        header.on('click:nav', function(type, uid) {
            lv = new list({collection: collection});
            if(type == 'hot') {
                lv.collection.url= "/activities?page=1&size=15&activity_id=1010&type=hot";
            }  else {
                lv.collection.url= "/activities?page=1&size=15&activity_id=1010";
            }
            lv.collection.type = type;
            window.app.show(layoutView._content, lv);
        });
        
    };
});
 
