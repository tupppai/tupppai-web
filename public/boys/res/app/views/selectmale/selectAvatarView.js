define(["app/views/base","tpl!app/views/selectmale/selectAvatarView.html"],function(e,t){"use strict";return e.extend({tagName:"div",className:"",template:t,initialize:function(){this.listenTo(this.model,"change",this.render),this.model.fetch()}})});