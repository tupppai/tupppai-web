$(function(){
	$(window).resize(infinite);
	function infinite() {
		var htmlWidth = $('html').width();
		if (htmlWidth >= 750) {
			$("html").css({
				"font-size" : "28px"
			});
		} else {
			$("html").css({
				"font-size" :  28 / 750 * htmlWidth + "px"
			});
		}
	}infinite();
});