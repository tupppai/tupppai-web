/**
 * 图片懒加载
 */
define(['zepto', 'lib/lazyload/lazyload'], function ($, lazyLoad) {
    "use strict";

    $.fn.imageLoad = function(options) {
        var defaults={
            "width":null,
            "height":null,
            "left":null,
            "right":null,
            "scrop":false,
            "scrop_function": null
        };
        var opts = $.extend({},defaults,options);
        $(this).lazyload({
            effect: "fadeIn",
            event: 'sporty',
            data_attribute: 'src',
            url_rewriter_fn:function($element, originalSrcInAttr){
                var url = originalSrcInAttr;
                //todo: upgrade
                if(!url) { url = $element.attr('src'); }
                var width = $element.width();
                var tag = '?imageView2/2/w/';
                if(width <= 100) { tag += 200; }
                else if(width <= 320) { tag += 320; }
                else if(width <= 400) { tag += 400; }
                else if(width <= 640) { tag += 640; }
                else { tag = ''; } 
                return url + tag;
            },
            load: function(image, count) {
                if(opts.scrop) {
                    scropLoad(image[0], opts);
                }
                opts.scrop_function && opts.scrop_function(image[0]);
            }
        }).trigger('sporty');
    };

    var scropLoad = function(el, opts) {
        var $this = $(el);
        var objHeight = $this.height(); //图片高度
        var objWidth = $this.width(); //图片宽度
        var parentHeight = opts.height||$this.parent().height(); //图片父容器高度
        var parentWidth = opts.width||$this.parent().width(); //图片父容器宽度
        var ratio = objHeight / objWidth;
        if (objHeight > parentHeight && objWidth > parentWidth) {
            if (objHeight < objWidth && !opts.right) { //赋值宽高
                $this.width(parentWidth);
                $this.height(parentWidth * ratio);
            } else {
                $this.height(parentHeight);
                $this.width(parentHeight / ratio);
            }
            objHeight = $this.height(); //重新获取宽高
            objWidth = $this.width();
            if (objHeight < objWidth && !opts.right) {
                $this.css("top", (parentHeight - objHeight) / 2);
                //定义top属性
            } else {
                //定义left属性
                $this.css("left", (parentWidth - objWidth) / 2);
            }
        }
        else {
            if (objWidth > parentWidth) {
                $this.css("left", (parentWidth - objWidth) / 2);
                $this.css("height", parentHeight);
            }
            $this.css("top", (parentHeight - objHeight) / 2);
        }

        if(opts.right) {  
            $this.css("right", opts.right);
        }
        if(opts.left) {  
            $this.css("left", opts.left);
        }
        $this.parent().css('overflow', 'hidden');
        $this.css('position', 'relative');
    };
});
