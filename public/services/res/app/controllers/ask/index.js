define(["app/views/ask/indexList/index"],function(e){"use strict";return function(){var t=["_view"],n=window.app.render(t),r=new window.app.collection;r.url="/v2/asks";var i=new e({collection:r});window.app.show(n._view,i)}});