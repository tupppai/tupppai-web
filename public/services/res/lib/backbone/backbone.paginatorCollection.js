(function(e,t){var n=e.Backbone;if(typeof define=="function"&&define.amd)return define(["backbone"],function(e){return t(e)});n.paginatorCollection=t(n)})(this,function(e){return e.Collection.extend({can_loading:!0,data:{page:1,size:15},initialize:function(){this.data={page:1,size:15}},load_more:function(e){var t=this;t.can_loading&&(t.can_loading=!1,t.data.page++,t.url.indexOf("page")>-1&&(t.url=t.url.split("?")[0]),t.url=t.url+"?page="+t.data.page,console.log(t.url),e=e?_.clone(e):{},e.success=function(n){n.length>0&&_.each(n,function(e){t.add(e),t.trigger("change")}),n.length<t.data.size&&e.finished(),n.length==t.data.size&&(e.not_finished(),t.can_loading=!0)},this.sync("read",this,e))}})});