define(['app/views/detail/detailContent/detailContent', "app/views/detail/recommendList/recommendList"], 
    function (detailContent, recommendList) {
    "use strict";
    return function(type, id, activity) {
        var sections = [ '_content', '_recommend'];
        var layoutView = window.app.render(sections);
        

        var model = new window.app.model();
        model.url= "/v2/thread/"+ type +"/" + id;
        var detail = new detailContent({
            model: model
        });
        window.app.show(layoutView._content, detail);  

        var collection = new window.app.collection();
        collection.url= "/populars?size=4&type=rand";
        var list = new recommendList({
            collection: collection
        });
        window.app.show(layoutView._recommend, list);  

        $("body").attr("data-type", type);

        detail.on('show', function() {
            var dataType = $("body").attr("data-type");
            var activity = $("body").attr("activity");

            $(".menuPs").addClass("hide");
            var picType;
            if(dataType == 1) {
                picType = "原图";
                title(picType);
                $(".ask-detail").removeClass("hide").text("查看作品");
            } else {
                picType = "作品";
                title(picType);
                $(".ask-detail").addClass("hide");
            };

            var desc = $(".workDesc").eq(0).text();
            var img = $(".sectionContent").eq(0).find("img").attr("src");
            var name = $(".userName").eq(0).text();
            //电影详情页面微信分享文案
            var options = {};
            options.title    = name + "的" + picType;
            options.desc    = desc;
            options.img    = img;
            if(activity) {
                $(".ask-detail").removeClass("hide").text("返回活动页").attr("href","#activity/index");
                options.title    = "我在参加#毕业照创意大比拼#，能不能见宋老公就靠你们点赞啦";
            }
            
            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){});
            share_zone(options,function(){},function(){});
            share_weibo(options,function(){},function(){});
            share_qq(options,function(){},function(){});
        });
        $('body').attr("activity", activity);

    };
});


