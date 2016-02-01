function getQueryVariable(variable, def) {
   var query = window.location.search.substring(1);
   var vars = query.split("&");
   for (var i=0;i<vars.length;i++) {
           var pair = vars[i].split("=");
           if(pair[0] == variable){return pair[1];}
   }

   return (def==undefined)?def:(false);
}
$(function() {

	var tutorial_id = getQueryVariable('tutorial_id', 0);
	$.get(
		'/thread/tutorial_details?tutorial_id='+tutorial_id,
		function(data) {
			var isStar = data.data.is_tar;
			if(isStar) {
				$(".middle-v-icon").removeClass("blo");
			};
			$(".course-head-left span img").attr("src", data.data.avatar);
			$(".course-name").html(data.data.nickname);
			$(".course-name-time").html(data.data.create_time);
			$(".explain-header").html(data.data.title);
			$(".explain-p").html(data.data.description);
			$(".course-big-pic a img").attr("src", data.data.image_url);
			$(".like-num").html(data.data.up_count);
			$(".see-num span").html(data.data.click_count);

		}


	)
})