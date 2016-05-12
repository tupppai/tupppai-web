define(['tpl!app/views/hot/reply/reply.html','waterfall'],
    function (template, waterfall) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'grid-item',
            template: template,
            onShow: function() {
                // 渲染瀑布流
                $('.grid').waterfall({
                  // options
                  root: '.grid',
                  itemSelector: '.grid-item',
                  columnWidth: $('.grid-item').width()/2;
                  var title_name = '【微出品】最新拍摄花絮和动态';
                  var desc = '追踪电影动态，和明星互动';

                  //电影详情页面微信分享文案
                  var options = {};
                  options.title   = title_name;
                  options.desc    = desc;
                  
                  share_friend(options,function(){},function(){})
                });
            },
        });
    });
