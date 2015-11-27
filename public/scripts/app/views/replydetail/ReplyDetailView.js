define([
        'app/views/Base', 
        'tpl!app/templates/replydetail/ReplyDetailView.html'
       ],
    function (View,  template, tmplate) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .written-reply": 'answer'
            },
            answer : function(e) {
                $(e.currentTarget).siblings(".reply-inp").css({
                    display: "block"
                }).parents(".comment-content").siblings().children(".reply-inp").css({
                    display: "none"
                })
            },
            onRender: function() {
                var timer = setTimeout(function(){
                    var height= $(".big-pic").height();
                    console.log($(".big-pic").height())
                    $(".reply-content").css({
                        height: $(".big-pic").height() + "px"
                    })
                }, 100)
               
            }
        });
    });
