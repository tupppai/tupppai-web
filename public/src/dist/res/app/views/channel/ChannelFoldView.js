define(["app/views/Base","tpl!app/templates/channel/ChannelFoldView.html"],function(e,t){"use strict";return e.extend({tagName:"div",className:"channel-fold",template:t,events:{},construct:function(){this.listenTo(this.collection,"change",this.render)}})});