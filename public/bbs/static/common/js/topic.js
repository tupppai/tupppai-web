$(document).ready(function(){        //DOM的onload事件处理函数
  $("#comment-submit").click(function(){          //当按钮button被点击时的处理函数
	  if(check_content()){
      var comment=$("#post_content").val();
	    postdata(comment); 
      //button被点击时执行postdata函数
      $("#post_content").val('').focus();//提交清空内容
	  }    
  });

  $('.row').delegate('.clickable', 'click', function() {
    var append_str = "@" + $(this).attr("data-mention") + " ";
    var reply_comment_row = $(this).parents('.row')[0];

    //隐藏所有评论框
    hideAllReplyArea();

    var reply_comment_area = $(reply_comment_row).find('.reply-comment-area');
    if (reply_comment_area.hasClass('hide')) {
      reply_comment_area.removeClass('hide');      
    } else {
      reply_comment_area.addClass('hide');
    }

    var reply_comment_textarea = $(reply_comment_row).find('.reply-comment-textarea');

    reply_comment_textarea.val('');
    reply_comment_textarea.insertAtCaret(append_str);
  });

  //评论的回复 点击提交
  $('.reply-comment-btn').click(function() {
    var reply_content_area = $(this).parents('.reply-comment-area');
    var reply_content = $(reply_content_area).find('.reply-comment-textarea').val();

    postdata(reply_content);
    //将所有的回复框隐藏掉
    $('.reply-comment-area').addClass('hide');
  }); 

  //@回复定位
  $(".reply").click(function(){
       $("#post_content").insertAtCaret('');
  });
});

//隐藏所有回复评论框
function hideAllReplyArea() {
  var reply_areas = $('.reply-comment-area');
  for (var i = 0; i < reply_areas.length; i++) {
    if (!$(reply_areas[i]).hasClass('hide')) {
      $(reply_areas[i]).addClass('hide');
    };
  };
}

/*
 * 提交评论数据
 */
function postdata(content) {
  var token   = $('#token').val();
  var comment = content;

  $.ajax({
    type      : 'POST',
    url       : baseurl + 'index.php/comment/add_comment',
    data      : "comment="+comment+"&topic_id="+$("#topic_id").val()+"&is_top="+$("#is_top").val()+"&username="+$("#username").val()+"&avatar="+$("#avatar").val()+"&lastpost="+$("#lastpost").val()+"&layer="+$("#layer").val()+"&csrf_token="+token, 
    dataType  : 'json',
    success   : function(msg) {
      if (msg.uid) {
        var html="<div class='row' id='r"+msg.layer+"'><div class='col-md-1'><a href='"+siteurl+"user/profile/"+msg.uid+"'><img class='img-rounded' src='"+msg.avatar+"' alt='"+msg.username+"'></a></div><div class='col-md-11 reply-body'><h4 class='topic-list-heading'><span><a href='"+siteurl+"user/profile/"+msg.uid+"'>"+msg.username+"</a>&nbsp;&nbsp;<small>"+msg.replytime+"</small></span><span class='pull-right' id='r"+msg.layer+"'>#"+msg.layer+" -<a href='#reply' class='clickable startbbs'  data-mention='"+msg.username+"'>回复</a></span></h4>"+msg.content+"</div><div class='col-md-11 right mt-l reply-comment-area hide'><textarea class='form-control reply-comment-textarea'></textarea><button class='right btn-primary btn reply-comment-btn mt-m'>回复</button></div></div><hr class='smallhr'>";
        
        html = $(html).hide();
        $('#comment_list').append(html);
        html.fadeIn();
      
        $('#comments').html(msg.layer);//改变回复数
        $('#lastpost').val(msg.lastpost);//更新最后时间
        $('#error').html('');    //清空错误
      } else {
        $('#error').html('<div class="alert alert-warning">'+msg.error+'</div>');//提示错误
      }
    }
  });
}

//快速回复ctrl+enter
    $(document).keypress(function(e){
        var active_id = document.activeElement.id;  
        if((e.ctrlKey && e.which == 13 || e.which == 10) && (active_id == "topic_content" || active_id == "post_content")) {
            e.preventDefault();
          //  $("#new_topic").submit();
            $("input[type=submit]").click();
        }
    });

function replyOne(username){
    replyContent = $("#post_content");
	oldContent = replyContent.val();
	prefix = "@" + username + " ";
	newContent = ''
	if(oldContent.length > 0){
	    if (oldContent != prefix) {
	        newContent = oldContent + "\n" + prefix;
	    }
	} else {
	    newContent = prefix
	}
	replyContent.focus();
	replyContent.val(newContent);
	moveEnd(replyContent);
}

function check_content(){
  if($("#post_content").val().length < 4){
    alert("对不起，回复内容不能少于4个字符！")
    $("#post_content").focus();
    return false;
  } else{
	 return true;
  }
}
