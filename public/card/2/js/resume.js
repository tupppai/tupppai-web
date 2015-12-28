$(function() {
    //专业技能
    $(".skill img").on("tap", function(e) {
        e.preventDefault(); 
        var skills = $(".skill").children("img");
        var skillNum = $(this).index();
        var skillTwo = (skillNum + 1) % 3;
        var skillThree = (skillNum + 2) % 3;

        $(this).animate({
            "left": "50%",
            "top": "2rem",
            "width": "6.16rem",
            "height": "6.16rem",
            "marginLeft": "-3.08rem"
        });
        skills.eq(skillThree).animate({
            "left": "13%",
            "top": "0",
            "width": "3.08rem",
            "height": "3.08rem",
            "marginLeft": "0"
        });
        skills.eq(skillTwo).animate({
            "left": "77%",
            "top": "0",
            "width": "3.08rem",
            "height": "3.08rem",
            "marginLeft": "0"
        });
        skills.removeClass("animated");
        $(this).parent().siblings("div").children().eq($(this).index()).addClass("appear").siblings().removeClass("appear");
    });

    //项目经验
    $(".slide-two div").on("tap", function(e) {
    	e.preventDefault();
        $(this).children("span").animate({
            height: "1.88rem"
        }).siblings("img").animate({
            height: "7.17rem",
            width: "7.17rem",
            marginLeft: "0"
        })
        $(this).children("p").animate({
            "height": "7.5rem"
        }).parent().siblings().children("p").animate({
            "height": "0"
        });
        $(this).siblings().children("img").animate({
            height: "4.17rem",
            width: "4.17rem",
            marginLeft: "1.5rem"
        }).siblings("span").animate({
            height: "1.2rem",
            lineHeight: "1.2rem",
        })
    });

    // 游戏
	var startDate = 0;
	var	endDate = 0;
	var zhon = 0;
	var timeArr = [];
	$(".game-one span").on("touchstart", function(e) {
		e.preventDefault();
		startDate = new Date();
		$(this).animate({
			"background": "#a67"
		});
	})
	$(".game-one span").on("touchend", function(e) {
		e.preventDefault();
		$(this).animate({
			"background": "#a33"
		});
		endDate = new Date();
		zhon = endDate - startDate;
		timeArr.push(zhon / 1000);
		var cun = zhon / 1000 + "s";
		$(".game-one h3").html("您估计的时间：" + cun);
		function show(a, b) {
			if(Math.abs(a - 2) > Math.abs(b - 2)) {
				return 1;
			}
			if(Math.abs(a - 2) == Math.abs(b - 2)) {
				return 0;
			}
			if(Math.abs(a - 2) < Math.abs(b - 2)) {
				return -1;
			}
		}
		timeArr.sort(show);
		var ter = "";
		for (var i = 0; i < timeArr.length; i++) {
			ter += "<li>第" + (i + 1) + "名：" + timeArr[i] + "</li>";
		};
		$(".game-one ul").html(ter);
	});

	$(".slide-six .game").on("tap", function(e) {
		e.preventDefault();
		$(this).find(".gamer").css({
			"display": "block"
		})
		$(this).siblings().find(".gamer").css({
			"display": "none"
		})
	})
    // 开关灯
    function switchLamp() {
        var lightNum = 0;
        var lightTimer = 10;
        var lightTime = null;
        var lightBoo = true;
        var lightPan = true;
        $(".light-num").html(lightNum);
        $(".light-time").html(lightTimer);
        $(".alerts").css({
            display: "none"
        })
        $(".light-on").on("tap", function(e) {
            e.preventDefault();
            if (lightPan) {
                lightNum++;
                $(".light-img").css({
                    background: "url(../images/light.png) center no-repeat",
                    backgroundSize: "cover"
                });
                $(".light-num").html(lightNum);
                if (lightBoo) {
                    lightTime = setInterval(function(){
                        lightTimer--;
                        if (lightTimer <= 0) {
                            clearInterval(lightTime);
                            $(".light-off").off();
                            $(".light-on").off();
                            $(".alerts").css({
                                display: "block"
                            })
                            $(".alerts div").html("你的分数为：" + lightNum);
                        };
                        $(".light-time").html(lightTimer);
                    }, 1000)
                };
                lightBoo = false;
                lightPan = false;           
            };
        })
        $(".light-off").on("tap", function(e) {
            e.preventDefault();
            if (lightPan == false) {
                lightNum++;
                $(".light-img").css({
                    background: "#000"
                });
                lightPan = true;
                $(".light-num").html(lightNum);
            };
        })
    };
    switchLamp();
    $(".alerts span").tap(function() {
        switchLamp()
    })

    var mySwiper = new Swiper ('.swiper-container', {
        direction: 'vertical',
        speed: 500,
        // initialSlide: 4,//固定从哪一页开始
        // 如果需要分页器
        pagination: '.swiper-pagination',  
        onInit: function(swiper){ //Swiper2.x的初始化是onFirstInit
            swiperAnimateCache(swiper); //隐藏动画元素 
            swiperAnimate(swiper); //初始化完成开始动画
        }, 
        onSlideChangeEnd: function(swiper){ 
            swiperAnimate(swiper); //每个slide切换结束时也运行当前slide动画
            $(".project-experience p").animate({
                "height": "0"
            });
            $(".slide-two div").children("img").css({
                width: "7.17rem",
                height: "7.17rem",
                marginLeft: "0"
            }).siblings("span").css({
                height: "1.88rem"
            });
            $(".game-one").animate({
                "display": "none"
            });
            $(".gamer").css({
                display: "none"
            })
        },
    });
})
