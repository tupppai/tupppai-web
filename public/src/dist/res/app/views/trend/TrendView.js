define(["app/views/Base","tpl!app/templates/trend/TrendView.html","pageResponse"],function(e,t,n){"use strict";return e.extend({tagName:"div",className:"",template:t,events:{"click .super-like":"superLike","mouseover .share-actionbar":"showSharePanel","mouseleave .trend-share":"hideSharePanel","click .trend-weibo":"shareWeibo","click .trend-qq":"shareQQ"},construct:function(){$("title").html("图派-动态页面"),$(".header-back").removeClass("height-reduce"),this.listenTo(this.collection,"change",this.render),this.scroll(),this.collection.loading(this.showEmptyView)},onRender:function(){$(".download").unbind("click").bind("click",this.download),this.loadImage(),$(window).width()<640&&n({selectors:".inner-container",mode:"auto",width:"728",height:"6874"})},showEmptyView:function(e){$(".inner-container .emptyContentView").empty(),$(".inner-container .emptyContentView").addClass("hide"),$(".addReplyMinHeight").addClass("ReplyMinHeight"),e.data.page==1&&e.length==0&&(append($("#contentView"),".emptyContentView"),$(".addReplyMinHeight").removeClass("ReplyMinHeight"))},showSharePanel:function(e){$(e.currentTarget).parent().find(".trend-share").show()},hideSharePanel:function(e){$(e.currentTarget).parent().find(".trend-share").hide()},shareWeibo:function(e){var t=$(e.currentTarget).data("type"),n=$(e.currentTarget).data("ask-id"),r=$(e.currentTarget).data("id"),i="http://www.tupppai.com/";t==1?mobShare.config({appkey:"de97f78883b2",params:{url:i+"#askdetail/ask/"+n,title:"#我在图派求p图#，从@图派itupai 分享，围观下"}}):t==2&&mobShare.config({appkey:"de97f78883b2",params:{url:i+"#replydetailplay/"+n+"/"+r,title:"大神太腻害，膜拜之！#图派大神# 从@图派itupai 分享，围观下"}});var s=mobShare("weibo");s.send()},shareQQ:function(e){var t=$(e.currentTarget).data("type"),n=$(e.currentTarget).data("ask-id"),r=$(e.currentTarget).data("id"),i=$(e.currentTarget).data("nick"),s=$(e.currentTarget).data("upload"),o=$(e.currentTarget).data("imageurl"),u="http://www.tupppai.com/";t==1?mobShare.config({appkey:"de97f78883b2",params:{url:u+"#askdetail/ask/"+n,title:"我分享了一张"+i+"的照片，速度求p!",pic:s,description:"＃图派，让你意想不到的图片社区"}}):t==2&&mobShare.config({appkey:"de97f78883b2",params:{url:u+"#replydetailplay/"+n+"/"+r,title:"我分享了一张"+i+"的照片，大神太腻害，膜拜之!",pic:o,description:"＃图派，让你意想不到的图片社区"}});var a=mobShare("qzone");a.send()}})});