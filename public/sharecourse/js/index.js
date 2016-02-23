$(function() {
	// 同意协议
	$(".agree input").on("tap", function() {
		$(this).toggleClass("check")
	});

	// 输入金钱
	$(".number li").on("tap", function() {
		$(this).addClass("change").find("em").removeClass("blo").parent("li").siblings("li").removeClass("change").find("em").addClass("blo");
	});

	// 收起投资
	$(".retract-icon").on("tap", function() {
		$(".i-want").addClass("blo")
	})

	// 弹出投资
	$(".us-product").on("tap", function() {
		$(".i-want").removeClass("blo")	
	})


	// tab
	$(".fundraising-head").on("tap", "span", function() {
		var _this = $(this);
		tab(_this, ".producer-explain", ".investment-description p");
	});	
	$(".team-head").on("tap", "span", function() {
		var _this = $(this);
		tab(_this, ".team-explain", ".team-description p");
	});
	$(".witness-head").on("tap", "span", function() {
		var _this = $(this);
		tab(_this, ".witness-explain", ".witness-description p");
	});

	// tab切换
	function tab(ele, tit, con) {
		var index = ele.index();

		ele.addClass("bordersha").find(".jian").removeClass("blo");
		ele.siblings("span").removeClass("bordersha").find(".jian").addClass("blo");
		$.get(
			'../json/content.json',
			function(data) {
				$(tit).text(data[index].tit);
				$(con).text(data[index].con);
			}
		)

	};

	
})