$(function() {
	var time;
	if(!tutorial_id) {
		tutorial_id = getQueryVariable('tutorial_id', 0);
	}
	$.get(
		// '../json/tutorial.json',
		'/thread/tutorial_details?tutorial_id=' + tutorial_id,
		function(data) {
			var isStar = data.data.is_star;

			time = data.data.create_time;
			time = moment.unix(time).locale('zh-cn').fromNow();

			if(isStar) {
				$(".middle-v-icon").remove();
			};
			for (var i = 0; i < data.data.ask_uploads.length; i++) {
				var newContainer = $(".course-big-pic").eq(0).clone();
				$(".course-big-pic a img").eq(i).attr("src", data.data.ask_uploads[i].image_url)
				newContainer.insertBefore($(".insert"));	
			};
			setTimeout(function() {
				$('.course-big-pic').eq(data.data.ask_uploads.length).addClass("blo");
			}, 500)

			$(".course-head-left span img").attr("src", data.data.avatar);
			$(".course-name").html(data.data.nickname);
			$(".course-name-time").html(time);
			$(".explain-header").html(data.data.title);
			$(".explain-p").html(data.data.description);
			$(".like-num").html(data.data.up_count);
			$(".see-num span").html(data.data.click_count);
		}
	)
})