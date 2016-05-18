define(['app/views/original/index/indexView', 'lib/component/asyncList'], 
	function (indexView) {
    "use strict";
    
    return window.app.list.extend({
        tagName: 'div',
        className: 'index-ask-pageSection clearfix grid',
    	childView: indexView,
        initialize: function() {
        },
        onRender: function() {
            title('帮P');
            $(".menuPs").removeClass("hide");
        },
        onShow: function() {
            this.$el.asynclist({
                root: this,
                renderMasonry: true,
                itemSelector: 'loading',
            });
        }
    });
});
