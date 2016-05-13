define(['app/views/ask/index/indexView', 'lib/masonry/masonry', 'lib/component/asyncList', 'lib/imagesloaded/imagesloaded'], 
	function (indexView, Masonry, asynclist, imagesLoaded) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'index-ask-pageSection clearfix grid',
    	childView: indexView,
        initialize: function() {
            this.listenTo(this.collection, 'add', this.append);
            this.$el.asynclist({
                collection: this.collection
            });
            //电影详情页面微信分享文案
            var options = {};
            //share_friend(options,function(){},function(){});
            //share_friend_circle(options,function(){},function(){})
        },
        append: function(item) {
            return false;
        },
        onRender: function() {
            title('帮P');
            $(".menuPs").removeClass("hide");
           
            var _this = this;
/*
            // 渲染瀑布流
            this.$el.waterfall({
              // options
              root: '.grid',
              itemSelector: '.grid-item',
              columnWidth: $('.grid-item').width()/2
            });
*/
            
           
        },
        //onShow: function() {
        //   imagesLoaded(this.$el, function() {
        //        var msnry = new Masonry(this.$el, {
        //            itemSelector: '.grid-item',
        //            columnWidth: 0
        //        });
        //    }); 
               
        //},
       // q_addChild: function(child, ChildView, index){
       //     var itemView = Marionette.CollectionView.prototype.addChild.apply(this, arguments);
       //     itemView.$el.css({ opacity: 0 });
       //     var self = this;

            //imagesLoaded(itemView.$el, function() {
        //        itemView.$el.css({ opacity: 1 });
          //      window.masonry.appended(itemView.$el);
            //    window.masonry.layout();
            //});   
       // }
    });
});
