define([
        'app/views/Base', 
        'tpl!app/templates/trend/TrendView.html'
       ],
    function (View,  template) {
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
                this.listenTo(this.collection, 'change', this.render);
                this.scroll();
                this.collection.loading(this.showEmptyView);
            debugger;
            },
            onRender: function() {
                $('.download').unbind('click').bind('click',this.download);
                this.loadImage(); 
            },
            showSharePanel: function(e) {
                $(e.currentTarget).parent().find('.trend-share').show();
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
