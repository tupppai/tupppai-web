define(["app/views/Base","app/collections/Banners","tpl!app/templates/index/IndexBannerView.html"],function(e,t,n){"use strict";var r="#indexBannerView div";return e.extend({tagName:"div",className:"swipe-wrap clearfix",template:n,collection:t,construct:function(){var e=this;this.listenTo(this.collection,"change",this.render),e.collection.loading()},render:function(){var e=this.template;this.collection.each(function(t){var n=e(t.toJSON());append(r,n)});var t=$(window).width();t<700&&setTimeout(function(){Swipe(document.getElementById("indexBannerView"))},1200),this.onRender()}})});