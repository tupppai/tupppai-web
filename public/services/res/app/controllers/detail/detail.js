define(["app/views/detail/detailView"],function(e){"use strict";return function(t,n){var r=["content"],i=window.app.render(r),s=new window.app.model;s.url="/v2/thread/"+t+"/"+n;var o=new e({model:s});window.app.show(i.content,o),$("body").attr("data-type",t)}});