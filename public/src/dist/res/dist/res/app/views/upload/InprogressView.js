define(["underscore","app/views/Base","tpl!app/templates/upload/InprogressView.html"],function(e,t,n){"use strict";return t.extend({tagName:"div",className:"",template:n,construct:function(){$(".inprogress-popup").fancybox({afterShow:function(){$(".reply-uploading-popup").unbind("click").bind("click",this.askImageUrl)}})},askImageUrl:function(e){}})});