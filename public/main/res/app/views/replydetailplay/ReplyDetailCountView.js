define(["app/views/Base","tpl!app/templates/replydetailplay/ReplyDetailCountView.html"],function(e,t){"use strict";return e.extend({tagName:"div",className:"",template:t,construct:function(){var e=this;this.listenTo(this.model,"change",this.render)}})});