define(["common","uploadify","app/views/Base"],function(e,t,n){"use strict";return n.extend({construct:function(){var e=this;$(".ask-uploading-popup").fancybox({afterShow:function(){$(".new-label span").unbind("click").bind("click",e.spanChange)}}),Common.upload("#upload_picture",function(t){$("#ask-uploading-popup input[name='show-picture']").val(t.data.url),$("#ask-uploading-popup .show-picture").attr("src",t.data.url),$(".upload-middle").addClass("opacity"),$(".show-picture").removeClass("opacity"),$("#upload_picture").attr("upload-id",t.data.id),$(".upload-accomplish").removeClass("disable").unbind("click").bind("click",e.upload)},null,{url:"/upload"})},spanChange:function(e){$(e.currentTarget).toggleClass("new-change")},upload:function(){if($("#ask-content-textarea").val().length>0&&$(".new-label span").hasClass("new-change")){var e=$("#upload_picture").attr("upload-id"),t=$("#attrChannelId").attr("data-id"),n=$("#ask-uploading-popup .ask-content").val(),r=[];for(var i=0;i<$(".new-label span").length;i++)$(".new-label span").eq(i).hasClass("new-change")&&r.push($(".new-label span").eq(i).attr("id"));if(!e)return error("上传求P图","上传求P图"),!1;$.post("asks/save",{upload_id:e,desc:n,tag_ids:r,category_id:t},function(e){toast("上传成功",function(){t&&t!=0?$("#check_more").click():(location.href="/#channel/ask",location.reload())})}),$("#upload_picture").attr("upload-id",""),$(".upload-accomplish").parent().parent().find(".ask-content").val("")}else alert("请描述并选择标签！")}})});