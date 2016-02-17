define([
        'app/views/Base', 
        'tpl!app/templates/trend/TrendView.html',
        'pageResponse'
       ],
    function (View,  template, pageResponse) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
                "click .super-like" : "superLike",
                "mouseover .share-actionbar": "showSharePanel",
                "mouseleave .trend-share": "hideSharePanel",
                "click .trend-weibo": "shareWeibo",
                "click .trend-qq": "shareQQ"
            },
            construct: function () {
                $("title").html("图派-动态页面");
                $('.header-back').removeClass("height-reduce");
                this.listenTo(this.collection, 'change', this.render);
                this.scroll();
                this.collection.loading(this.showEmptyView);
            },
            onRender: function() {
                $('.download').unbind('click').bind('click',this.download);
                this.loadImage(); 
                if($(window).width() < 640) {
                   pageResponse({
                        selectors: '.inner-container',     //模块的类名，使用class来控制页面上的模块(1个或多个)
                        mode : 'auto',     // auto || contain || cover 
                        width : '728',      //输入页面的宽度，只支持输入数值，默认宽度为320px
                        height : '6874'      //输入页面的高度，只支持输入数值，默认高度为504px
                    })
                }
            },
            showEmptyView: function(data) {
                $(".inner-container .emptyContentView").empty();
                $(".inner-container .emptyContentView").addClass('hide');
                $(".addReplyMinHeight").addClass('ReplyMinHeight');
                if(data.data.page == 1 && data.length == 0) {
                    append($("#contentView"), ".emptyContentView");
                    $(".addReplyMinHeight").removeClass('ReplyMinHeight');
                }
            },
            showSharePanel: function(e) {
                $(e.currentTarget).parent().find('.trend-share').show();
                //TODO 根据分享类型改变二维码
            },
            hideSharePanel: function(e) {
                $(e.currentTarget).parent().find('.trend-share').hide();
            },
            shareWeibo: function(e) {
                var type = $(e.currentTarget).data('type');
                var ask_id = $(e.currentTarget).data('ask-id');
                var id = $(e.currentTarget).data('id');
                var base_url = "http://www.tupppai.com/";

                //原图
                if (type == 1) {
                    mobShare.config({
                        appkey: 'de97f78883b2',
                        params: {
                            url: base_url + '#askdetail/ask/' + ask_id,
                            title: '#我在图派求p图#，从@图派itupai 分享，围观下'
                        }
                    });
                } else if (type == 2) {
                    mobShare.config({
                        appkey: 'de97f78883b2',
                        params: {
                            url: base_url + '#replydetailplay/' + ask_id + '/' + id,
                            title: '大神太腻害，膜拜之！#图派大神# 从@图派itupai 分享，围观下'
                        }
                    });
                }
                
                var weibo = mobShare('weibo');
                weibo.send();
            },
            shareQQ: function(e) {
                var type = $(e.currentTarget).data('type');
                var ask_id = $(e.currentTarget).data('ask-id');
                var id = $(e.currentTarget).data('id');
                var nick = $(e.currentTarget).data('nick');
                var upload = $(e.currentTarget).data('upload');
                var image_url = $(e.currentTarget).data('imageurl');
                var base_url = "http://www.tupppai.com/";

                //原图
                if (type == 1) {
                    mobShare.config({
                        appkey: 'de97f78883b2',
                        params: {
                            url: base_url + '#askdetail/ask/' + ask_id,
                            title: '我分享了一张' + nick + '的照片，速度求p!',
                            pic: upload,
                            description: '＃图派，让你意想不到的图片社区'
                        }
                    });
                } else if (type == 2) {
                    mobShare.config({
                        appkey: 'de97f78883b2',
                        params: {
                            url: base_url + '#replydetailplay/' + ask_id + '/' + id,
                            title: '我分享了一张' + nick + '的照片，大神太腻害，膜拜之!',
                            pic: image_url,
                            description: '＃图派，让你意想不到的图片社区'
                        }
                    });
                }
                
                var qzone = mobShare('qzone');
                qzone.send();
            }
        });
    });
