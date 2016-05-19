define(['app/views/original/detailList/index'], 
    function (list) {
    "use strict";
    return function(ask_id, reply_id) {
        var sections = [ 'content'];
        var layoutView = window.app.render(sections);

        var collection = new window.app.collection();
        collection.url= "/v2/replies/ask/"+ ask_id;
        var lv = new list({
            collection: collection
        });
        window.app.show(layoutView.content, lv);   

        lv.on('show', function() {
            var img = $(".sectionContent").eq(0).find("img").attr("src");
            var desc = $(".workDesc").eq(0).text();
            //电影详情页面微信分享文案
            var options = {};
            options.title    = "图片分享";
            options.desc    = desc;
            options.img    = img;

            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){});
        }); 

    };
});


