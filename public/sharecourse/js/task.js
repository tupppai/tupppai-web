function getQueryVariable(variable, def) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
       var pair = vars[i].split("=");
       if(pair[0] == variable){return pair[1];}
	}

   return (def==undefined)?def:(false);
};

$(function() {

	var reply_id = getQueryVariable('reply_id', 0);
	var tutorial_id = getQueryVariable('tutorial_id', 0);
	$.get(
		'/ask/show/ask_id?reply_id=' + reply_id + '/tutorial_id=' + tutorial_id;
		function(data) {
			var isStar = data.data[0].is_tar;
			console.log(data)
			if(isStar) {
				$(".middle-v-icon").removeClass("blo");
			};
			$(".task-head-left span img").attr("src", data.data[0].avatar);
			$(".task-name").html(data.data[0].nickname);
			$(".task-name-time").html(data.data[0].create_time);
			$(".explain-header").html(data.data[0].title);
			$(".explain-p").html(data.data[0].description);
			$(".ask-pic").attr("src", data.data[0].image_url);
			$(".task-ask-pic").attr("src", data.data[0].image_url);
			$(".introduce").html(data.data[0].desc);
			$(".forward-num span").html(data.data[0].share_count);
			$(".task-comment span").html(data.data[0].comment_count);
			$(".like-num").html(data.data[0].up_count);

		}


	)
})