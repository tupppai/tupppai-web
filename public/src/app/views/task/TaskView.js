 define([
        'app/views/Base',
        'tpl!app/templates/task/TaskView.html',
       ],
    function (View, template) {

        "use strict";
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            // events: {
            //     "click .task-super-like" : "takeLike",
            // },
            // takeLike: function(e) {
            //     var value = $(e.currentTarget).attr('data-love');
            //     var id   = $(e.currentTarget).attr('data-id');
            //     var likeEle = $(e.currentTarget).find('.like-count');
            //     var type   = 2;
            //     value++;
            //     if( value > 3 ) {
            //         toast("点满三次了,不能在点击了亲!");
            //         return false;
            //     }
            //     value--;
            //     $.get('/love', {
            //         id: id,
            //         num: value,
            //         type: 2,
            //     }, function(data) {
            //         if( data.ret != 1) {
            //             var data = parse(data);
                    
            //         } else {
            //             value++;
            //             if(value == 1) {
            //                 $(e.currentTarget).attr("data-love", value).removeClass("task-like").addClass("task-like-one");
            //                 // $(e.currentTarget).find(".bg-sprite-rebirth").removeClass("like-icon").addClass("like-icon-one");

            //                 $(e.currentTarget).addClass('liked');
            //                 $(e.currentTarget).find('.like-count').toggleClass('like-color');

            //                 likeEle.text( Number(likeEle.text())+ 1 );
            //             }                
            //             if(value == 2) {
            //                 $(e.currentTarget).attr("data-love", value).removeClass("task-like-one").addClass("task-like-two");
            //                 // $(e.currentTarget).find(".bg-sprite-rebirth").removeClass("like-icon-one").addClass("like-icon-two");
            //                 $(e.currentTarget).find('.like-count').toggleClass('like-color');

            //                 likeEle.text( Number(likeEle.text())+ 1 );
            //             }                
            //             if(value == 3) {
            //                 $(e.currentTarget).attr("data-love", value).removeClass("task-like-two").addClass("task-like-three");
            //                 // $(e.currentTarget).find(".bg-sprite-rebirth").removeClass("like-icon-two").addClass("like-icon-three");

            //                 $(e.currentTarget).addClass('liked');
            //                 $(e.currentTarget).find('.like-count').toggleClass('like-color');

            //                 likeEle.text( Number(likeEle.text())+ 1 );
            //             }
            //         }
            //     });
            // },
            construct: function() {
                this.listenTo(this.collection, 'change', this.render);
                this.scroll();
                this.collection.loading(this.showEmptyView);
            }, 
            onRender: function(){ 
                var htmlWidth = $('html').width();
                if (htmlWidth >= 750) {
                    $("html").css({
                        "font-size" : "28px"
                    });
                } else {
                    $("html").css({
                        "font-size" :  28 / 750 * htmlWidth + "px"
                    });
                }
                $(window).resize(function() {
                    var htmlWidth = $('html').width();
                    if (htmlWidth >= 750) {
                        $("html").css({
                            "font-size" : "28px"
                        });
                    } else {
                        $("html").css({
                            "font-size" :  28 / 750 * htmlWidth + "px"
                        });
                    }
                });
                setTimeout(function(e) {

                    var imageWidth  = $(".arwork-pic img").attr("width");
                    var imageHeight = $(".arwork-pic img").attr("height");
                    var imageRatio  = imageWidth / imageHeight;
                    var containerWidth      = $('#aaaa').width();
                    var containerHeight     = $('#aaaa').height();
                    var tempWidth  = 0;
                    var tempHeight = 0;
                    var offsetLeft = 0;
                    var offsetTop  = 0;

                    if (imageHeight >= containerHeight && imageWidth >= containerWidth) {
                        // 图片宽高都大于容器宽高

                        // 图片长比较长，按照高度缩放，截取中间部分
                        if (imageWidth / imageHeight >= containerWidth / containerHeight) {
                          

                            tempHeight = containerHeight;
                            tempWidth  = imageWidth * containerHeight / imageHeight;

                            offsetLeft = (containerWidth - tempWidth) / 2;
                            offsetTop  = 0; 
                        } else {
                            //图片比较高，安装宽度缩放，截取中间部分
                            tempWidth  = containerWidth;
                            tempHeight = imageHeight * containerWidth / imageWidth;

                            offsetLeft = 0;
                            offsetTop  = (containerHeight - tempHeight) / 2;
                        };    
                    } else if (imageWidth <= containerWidth && imageHeight <= containerHeight) {
                        // 图片宽高都小于容器宽高
                        if (imageRatio > containerWidth / containerHeight) {
                            tempHeight   = containerHeight;
                            tempWidth    = imageWidth * containerHeight / imageHeight;

                            offsetTop    = 0;
                            offsetLeft   = (imageWidth - tempWidth) / 2;
                        } else {
                            tempWidth    = containerWidth;
                            tempHeight   = imageHeight * containerWidth / imageWidth;

                            offsetLeft   = 0;
                            offsetTop    = (imageHeight - tempHeight) / 2;
                        }
                    } else if (imageWidth <= containerWidth && imageHeight > containerHeight) {
                        // 图片宽度小于容器 高度大于容器  
                        tempWidth  = containerWidth;
                        tempHeight = imageHeight * containerWidth / imageWidth;

                        offsetTop  = (imageHeight - tempHeight) / 2;
                        offsetLeft = 0;
                    } else if (imageWidth > containerWidth && imageHeight <= containerHeight) {
                        // 图片宽度大于容器 图片高度小于容器
                        tempHeight = containerHeight;
                        tempWidth  = imageRatio * containerHeight;

                        offsetLeft = (imageWidth - tempWidth) / 2;
                        offsetTop  = 0;
                    };          
           
                    $(".arwork-pic img").css('left', offsetLeft);
                    $(".arwork-pic img").css('top', offsetTop);
                    $(".arwork-pic img").width(tempWidth);
                    $(".arwork-pic img").height(tempHeight);   
                }, 1500)
            },
        });
    });
