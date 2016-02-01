$(function() {
	setTimeout(function() {
		var time;
		var reply_id = getQueryVariable('reply_id', 0);
		if(!tutorial_id) {
			tutorial_id = getQueryVariable('tutorial_id', 0);
		}
		$.get(
			'/ask/show/' + tutorial_id + '?reply_id=' + reply_id,
			function(data) {
				var length = data.data.length;
				for(var i = 0; i< length; i++) {
					var newData = data.data[i];
					var newContainer = $(".task-contain").eq(0).clone();

					var isStar = newData.is_star;
					if(isStar) {
						newContainer.find(".middle-v-icon").removeClass("blo");
					};
					time = newData.create_time;
					time = moment.unix(time).locale('zh-cn').fromNow();

					newContainer.find(".task-head-left span img").attr("src", newData.avatar);
					newContainer.find(".task-name").html(newData.nickname);
					newContainer.find(".task-name-time").html(time);
					newContainer.find(".explain-header").html(newData.title);
					newContainer.find(".explain-p").html(newData.description);
					newContainer.find(".ask-pic").attr("src", newData.image_url);
					newContainer.find(".task-ask-pic").attr("src", newData.image_url);
					newContainer.find(".introduce").html(newData.desc);
					newContainer.find(".forward-num span").html(newData.share_count);
					newContainer.find(".task-comment span").html(newData.comment_count);
					newContainer.find(".like-num").html(newData.up_count);
					newContainer.insertBefore($(".course-fix"));
				}
			}
		)
	},100)
	setTimeout(function() {
		$('.task-contain').eq(0).addClass("blo");
	}, 200)
})