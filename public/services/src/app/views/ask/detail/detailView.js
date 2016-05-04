define(['tpl!app/views/ask/detail/detail.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'detail-padding',
            template: template,
            events: {
            	"click .commentLine": "replyPopup",
            	"click .cancel": "replyPopupHide",
                "click .window-fix": "windowFix",
                "click .comment-btn": "commons",
            	"click .like-btn": "clickLike",
                "click .share": 'clickShare',
                "click .share-mask" : "clickShare",
                "click .footerHelp" : "download",
            },
            // 分享朋友
            clickShare: function(e) {
                $(".share-mask").removeClass("hide");
                if($(e.target).hasClass("share-mask")) {
                    $(".share-mask").addClass("hide");
                };
            },
            download: function(e) {
                var type = $(e.currentTarget).attr("type");
                var id   = $(e.currentTarget).attr("id");
                var category_id = $(e.currentTarget).attr("category-id");
                
                if( category_id == 'undefine' ) {
                    var category_id = 0;
                }

                $.get('/record?type='+ type +'&target='+ id +'&category_id='+ category_id, function(data) {
                    parse(data);
                    console.log(data)
                    if(data.ret == 1) {
                        var data = data.data;
                        var urls = data.url;
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                            console.log(location.href)
                        });
                        toast('已下载该图片，到进行中处理');
                    }
                });
            },

            clickLike: function(e) {
                var loveCount = $(e.currentTarget).attr('love-count');
                var id   = $(e.currentTarget).attr('id');
                var likeEle = $(e.currentTarget).find('.text-like-btn');
                var type   = 2;
                $.get('/v2/love', {
                    id: id,
                    num: loveCount,
                    type: 2
                }, function(data) {
                    $(e.currentTarget).addClass("liked-icon")
                    likeEle.text( Number(likeEle.text()) + 1 );
                })
            },
            replyPopup: function(e) {
            	$("#replyWindow").removeClass("hide");
                var name = $(e.currentTarget).find(".userName-reply").text();
                debugger;
                $(".replyTo").text(name)
            },            
            replyPopupHide: function(e) {
            	$(".window-fix").addClass("hide");
            },            
            windowFix: function(e) {
            	if($(e.target).hasClass("window-fix")) {
            		$(e.currentTarget).addClass("hide");
            	}
            },
            commons: function(e) {
                $("#commentWindow").removeClass("hide")
            }
        });
    });


