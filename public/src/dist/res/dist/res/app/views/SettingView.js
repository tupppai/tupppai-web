define(["common","app/views/Base","tpl!app/templates/SettingView.html"],function(e,t,n){"use strict";return t.extend({tagName:"div",className:"",template:n,events:{"click .base-nav":"navBar"},navBar:function(e){var t=$(e.currentTarget).attr("data-type");location.href="#setting/"+t,setTimeout(function(){location.reload()},100)}})});