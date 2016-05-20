define(['app/views/activity/list/index', 'app/views/activity/header/headerView'], 
	function (list, headerView) {
    "use strict";
    return function(uploadReturn) {
        var sections = ['_header', '_content'];
        var layoutView = window.app.render(sections);

        var header = new headerView({
        });
        window.app.show(layoutView._header, header);         

        var collection = new window.app.collection();
        collection.url= "/activities?page=1&size=15&activity_id=1010";
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

            //电影详情页面微信分享文案
            if(uploadReturn) {
                $(".mongolia-layer").removeClass("hide");
                var desc = $("body").attr("desc");
                var img = $("body").attr("img_url");
                var reply_id = $("body").attr("reply_id");
                var options = {};
                options.title    = "我在参加#毕业照创意大比拼#，能不能见宋老公就靠你们点赞啦";
                options.desc    = desc;
                options.img    = img;
                options.link = location.host + "/services/index.html#detail/detail/2/" + reply_id;
                
                share_friend(options,function(){},function(){});
                share_friend_circle(options,function(){},function(){});
            } else {
                var img = $(".sectionContent").eq(0).find("img").attr("src");
                var title = $(".activity-title").text();
                var options = {};
                options.title    = title;
                options.desc    = "晒创意毕业照赢宋仲基粉丝见面会门票啦";
                options.img    = "http://7u2spr.com1.z0.glb.clouddn.com/20160519-170929573d82c99d903.jpeg?imageView2/2/w/480";
                
                share_friend(options,function(){},function(){});
                share_friend_circle(options,function(){},function(){});
            }
        });    

        header.on('click:nav', function(type, uid) {
            lv = new list({collection: collection});
            if(type == 'hot') {
                lv.collection.url= "/activities?page=1&size=15&activity_id=1010&type=" + type;
            }  else {
                lv.collection.url= "/activities?page=1&size=15&activity_id=1010";
            }
            lv.collection.type = type;
            window.app.show(layoutView._content, lv);
        });
        $("body").attr("uploadReturn", uploadReturn);
        
    };
});
 
