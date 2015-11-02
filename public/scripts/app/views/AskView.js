define([
        'app/views/Base', 
        'app/models/Base', 
        'app/models/Like', 
        'app/collections/Asks', 
        'tpl!app/templates/AskItemView.html',
        'tpl!app/templates/AskCardView.html'
       ],
    function (View, ModelBase, Like, Asks, template, AskCardView) {
        "use strict";
        
        return View.extend({
            collection: Asks,
            tagName: 'div',
            className: 'photo-container',
            template: template,
            events: {
                'click .like_toggle' : 'askLikeToggle',
                "click .photo-item-reply" : "photoShift",
                "click .download" : "downloadClick",
                "click .appDownload" : "appDownloadActive"
            },
            askLikeToggle: function(e) {
                var value = 1;
                if( $(e.currentTarget).hasClass('icon-like-pressed') ){
                    value = -1;
                }

                var id = $(e.target).attr('data-id');
                var like = new Like({
                    id: id,
                    type: 1,
                    status: value 
                });

                like.save(function(){

                    $(e.currentTarget).toggleClass('icon-like-pressed');
                    $(e.currentTarget).siblings('.actionbar-like-count').toggleClass('icon-like-color');

                    var likeEle = $(e.currentTarget).siblings('.actionbar-like-count');
                    var linkCount = likeEle.text( Number(likeEle.text())+value );
                });
            },
            // 点击求P免费上传求P图 跳到APP下载页面导航显示
            appDownloadActive: function(e) {
                $("a.menu-bar-item").removeClass('active');
                $("a.menu-bar-item[href='#download']").addClass('active');       
            },
            // 求助图片切换
            photoShift: function(e) {
                 var AskSmallUrl = $(e.currentTarget).find('img').attr("src");
                 var AskLargerUrl = $(e.currentTarget).prev().find('img').attr("src");
                 $(e.currentTarget).prev().find('img').attr("src",AskSmallUrl);
                 $(e.currentTarget).find('img').attr("src",AskLargerUrl);              
            },
            construct: function () {
                var self = this;
                self.listenTo(self.collection, 'change', self.render);

                self.scroll();
                self.collection.loadMore();
            },
            flag: true,
            render: function() {
                var template = this.template;
                var el = this.el;
                if(this.flag) {
                    var ask_card_view = AskCardView();
                    append(el, ask_card_view);
                    $(el).find(".photo-container").css("display", "inline");

                    this.flag = false;
                }
                this.collection.each(function(model){
                    append(el, template(model.toJSON()));
                });

                this.onRender(); 
            },
             downloadClick: function(e) {
                var data = $(e.currentTarget).attr("data");
                var id   = $(e.currentTarget).attr("data-id");

                var model = new ModelBase;
                model.url = '/record?type='+data+'&target='+id;
                model.fetch({
                    success: function(data) {
                        var urls = data.get('url');
                        _.each(urls, function(url) {
                            location.href = '/download?url='+url;
                        });

                    }
                });
            },
        });
    });
