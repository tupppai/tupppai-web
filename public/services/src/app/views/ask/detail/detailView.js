define(['tpl!app/views/ask/detail/detail.html'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: '',
            template: template,
            initialize: function() {
                this.listenTo(this.model, 'change', this.render);
            },
            events: {
            	"click .commentLine": "replyPopup",
            	"click .cancel": "replyPopupHide",
                "click .window-fix": "windowFix",
                "click .comment-btn": "commons",
            	"click .like-btn": "clickLike",
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


