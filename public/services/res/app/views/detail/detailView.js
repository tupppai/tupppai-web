define(["tpl!app/views/detail/detail.html"],function(e){"use strict";return window.app.view.extend({tagName:"div",className:"",template:e,events:{"click .commentDetail":"replyPopup","click .cancel":"replyPopupHide","click .window-fix":"windowFix","click .comment-btn":"commons","click .like-btn":"clickLike","click .share":"clickShare","click .share-mask":"clickShare","click .footerHelp":"download","click #replySend":"worksComment","click #replyComment":"replyComment"},onShow:function(){var e=$("body").attr("data-type");$(".menuPs").addClass("hide");var t;e==1?(t="原图",title(t),$(".ask-detail").text("查看作品")):(t="作品",title(t),$(".ask-detail").text("查看原图"));var n=$(".workDesc").eq(0).text(),r=$(".sectionContent").eq(0).find("img").attr("src"),i=$(".userName").eq(0).text(),s={};s.title=i+"的"+t,s.desc=n,s.img=r,share_friend(s,function(){},function(){}),share_friend_circle(s,function(){},function(){})},clickShare:function(e){$(".share-mask").removeClass("hide"),$(e.target).hasClass("share-mask")&&$(".share-mask").addClass("hide")},worksComment:function(e){var t=$(e.currentTarget).attr("dataId"),n=$(e.currentTarget).attr("dataType"),r=$(e.currentTarget).attr("inset"),i=$("#commentWindow .windowContent").val();if(!i||i=="")return toast("内容不能为空"),!1;$.post("/v2/comments/save",{id:t,type:n,content:i},function(e){var t=e.comment_id,n=e.reply_to,i=e.target_id,s=e.data_type,o=e.user_name,u=e.content,a='<div data-type="'+s+'"'+"target-id="+'"'+i+'"'+"reply-to="+'"'+n+'"'+"comment-id="+'"'+t+'"'+' class="commentDetail"><div class="commentLine"><div class="commentHead clearfix"><span class="userName userName-reply">'+o+'：</span><div class="commentOption"><span class="optionItem reply">回复</span></div></div><span class="commentText">'+u+"</span></div></div>";$("#"+r).after(a),$("#"+r).siblings(".commentDetail").eq(3).remove(),$(".windowContent").val(""),$("#commentWindow").addClass("hide");var f="评论成功";fntoast(f)})},replyComment:function(e){var t=$(e.currentTarget),n=t.attr("inset"),r=t.parents("#replyWindow").find(".windowContent").val(),i=t.attr("reply-to"),s=t.attr("data-type"),o=t.attr("comment-id"),u=t.attr("target-id"),a={content:r,type:s,id:u,reply_to:i,for_comment:o};$.post("/v2/comments/save",a,function(e){var t=e.comment_id,r=e.reply_to,i=e.target_id,s=e.data_type,o=e.user_name,u=e.content,a=e.reply_name,f='<div data-type="'+s+'"'+"target-id="+'"'+i+'"'+"reply-to="+'"'+r+'"'+"comment-id="+'"'+t+'"'+' class="commentDetail"><div class="commentLine commentReply"><div class="commentHead clearfix"><span class="userNameGroup"><span class="userName-reply">'+o+'</span><em>回复</em><span class="userName-beReplied">'+a+':</span></span><div class="commentOption"><span class="optionItem reply">回复</span></div></div><span class="commentText">'+u+"</span></div></div>";$("#"+n).after(f),$("#"+n).siblings(".commentDetail").eq(3).remove(),$(".windowContent").val(""),$("#replyWindow").addClass("hide");var l="回复成功";fntoast(l)})},download:function(e){var t="长按图片即可下载图片";fntoast(t)},clickLike:function(e){var t=$(e.currentTarget).attr("love-count"),n=$(e.currentTarget).attr("data-id"),r=$(e.currentTarget).find(".text-like-btn"),i=2;$(e.currentTarget).hasClass("liked-icon")||$.get("/v2/love",{id:n,num:t,type:2},function(t){$(e.currentTarget).addClass("liked-icon"),r.text(Number(r.text())+1);var n="点赞成功";fntoast(n)})},replyPopup:function(e){$("#replyWindow").removeClass("hide");var t=$(e.currentTarget).find(".userName-reply").text();$(".replyTo").text(t);var n=$(e.currentTarget).attr("target-id"),r=$(e.currentTarget).attr("comment-id"),i=$(e.currentTarget).attr("reply-to"),s=$(e.currentTarget).attr("data-type"),o=$(e.currentTarget).siblings(".sectionFooter").attr("id");$("#replyComment").attr("target-id",n),$("#replyComment").attr("comment-id",r),$("#replyComment").attr("reply-to",i),$("#replyComment").attr("data-type",s),$("#replyComment").attr("inset",o)},commons:function(e){$("#commentWindow").removeClass("hide");var t=$(e.currentTarget).attr("data-id"),n=$(e.currentTarget).attr("data-type"),r=$(e.currentTarget).parents(".sectionFooter").attr("id");$("#replySend").attr("dataId",t),$("#replySend").attr("dataType",n),$("#replySend").attr("inset",r)},replyPopupHide:function(e){$(".window-fix").addClass("hide")},windowFix:function(e){$(e.target).hasClass("window-fix")&&$(e.currentTarget).addClass("hide")}})});