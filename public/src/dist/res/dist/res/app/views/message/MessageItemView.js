define(["app/views/Base","tpl!app/templates/message/MessageItemView.html"],function(e,t){"use strict";return e.extend({tagName:"div",className:"",template:t,construct:function(){$("a.menu-bar-item").removeClass("active"),this.listenTo(this.collection,"change",this.render),this.scroll(),this.collection.loading(this.showEmptyView)},showEmptyView:function(e){e.data.page==1&&e.length==0&&append($("#contentView div"),".emptyContentView")}})});