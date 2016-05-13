define(['tpl!app/views/hot/reply/reply.html', 'imageLazyLoad'],
    function (template) {
        "use strict";
        
        return window.app.view.extend({
            tagName: 'div',
            className: 'loading',
            template: template,
            onShow: function() {
                // this.scropLoad('.imageLoad', "true")
                this.$('.imageLoad').imageLoad({scrop: true});
            },
            scropLoad : function(el, opts) {
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
            }
        });
    });
