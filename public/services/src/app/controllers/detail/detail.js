define(['app/views/detail/detailView'], 
    function (detailView) {
    "use strict";
    return function(type, id) {
        var sections = [ 'content'];
        var layoutView = window.app.render(sections);

        var model = new window.app.model();
        model.url= "/v2/thread/"+ type +"/" + id;
        var detail = new detailView({
            model: model
        });
        window.app.show(layoutView.content, detail);  
        $("body").attr("data-type", type);

        detail.on('show', function() {
            var dataType = $("body").attr("data-type");
            $(".menuPs").addClass("hide");
            var picType;
            if(dataType == 1) {
                picType = "原图";
                title(picType);
                $(".ask-detail").text("查看作品");
            } else {
                picType = "作品";
                title(picType);
                $(".ask-detail").text("查看原图");
            };

            var desc = $(".workDesc").eq(0).text();
            var img = $(".sectionContent").eq(0).find("img").attr("src");
            var name = $(".userName").eq(0).text();
            //电影详情页面微信分享文案
            var options = {};
            options.title    = name + "的" + picType;
            options.desc    = desc;
            options.img    = img;
            
            share_friend(options,function(){},function(){});
            share_friend_circle(options,function(){},function(){});
        }); 

    };
});


