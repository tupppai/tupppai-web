define(["common","uploadify","app/views/Base"],function(e,t,n){"use strict";return n.extend({construct:function(){var e=this;$(".reply-uploading-popup").fancybox({afterShow:function(){}}),Common.upload("#upload_picture-reply",function(t){$("#reply-uploading-popup input[name='show-picture']").val(t.data.url),$("#reply-uploading-popup .show-picture").attr("src",t.data.url),$(".upload-middle").addClass("opacity"),$(".show-picture").removeClass("opacity"),$("#upload_picture-reply").attr("upload-id",t.data.id),$(".upload-accomplish").removeClass("disable").unbind("click").bind("click",e.upload)},null,{url:"/upload"})},upload:function(e){var t=$("#upload_picture-reply").attr("upload-id"),n=$("#reply-uploading-popup").attr("ask-id"),r=$("#reply-uploading-popup .reply-content").val(),i=$("#reply-uploading-popup").attr("data-id");if(!r)return error("提示","内容不能为空"),!1;if(!t)return error("上传作品","请上传作品"),!1;$.post("/replies/save",{ask_id:n,upload_id:t,category_id:i,desc:r},function(e){$.fancybox.close(),i?(location.href="/#channel/"+i,location.reload()):(location.href="/#channel/reply",location.reload()),$(".title-bar").removeClass("hide"),$(".header-back").removeClass("height-reduce"),$(".reply-index").addClass("active").siblings().removeClass("active"),toast("上传成功",function(){})});var s="http://7u2spr.com1.z0.glb.clouddn.com/20151205-154952566297205441e.png";$(".upload-middle").removeClass("opacity"),$(".show-picture").attr("src",s),$("#upload_picture-reply").attr("upload-id",""),$("#reply-uploading-popup").attr("ask-id",""),$(".upload-accomplish").parent().parent().find(".reply-content").val("")}})});