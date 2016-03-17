define(['app/views/base', 'tpl!app/views/selectmale/SelectMaleGodsView.html', 'swiper', 'fx'],
    function (View, template, Swiper, fx) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .designer-pic li": "switchPic",
            	"click .designer-effect li": "switchPic",
            },
            initialize: function() {
                  this.listenTo(this.model, 'change', this.render);
                  this.model.fetch();
            },
            //点击效果图的时候替换轮播图片
            switchPic: function(e) {
            	var num = $(e.currentTarget).index();
            	var index = $(".pic-box").attr("index"),
					src = $(".designer-pic").eq(index).find("li").eq(num).find("img").attr("src"), //获取点击图片的src
					scrollLeft = document.documentElement.scrollLeft || document.body.scrollLeft || 0,
				    scrollTop = document.documentElement.scrollTop || document.body.scrollTop || 0;

				$("#flyItem").find("img").attr("src", src); //替换飞快的src
			    $("#flyItem").css({
			    	left: event.clientX + scrollLeft + "px",
			    	top: event.clientY + scrollTop + "px",
			    	visibility : "visible",
			    	opacity: "0",
			    	transform: "scale(1)"
			    });
			    $("#flyItem").animate ({
			    	left: "50%",
			    	top: "9.71rem",
			    	marginLeft: "-1rem",
			    	visibility : "hidden",
			    	opacity: "1",
			    	transform: "scale(6.1)"
			    }, 300);
			    setTimeout(function() {
					$(".main-img").eq(index).attr("src", src);
			    }, 310)
            },
   			onRender: function() {
				var index = window.location.hash.substr(1); //获取url上的索引值
				$(".my-make").attr("index", index);

				var mySwiper = new Swiper('.swiper-container',{
					slidesPerView : 'auto',
					centeredSlides : true,
					watchSlidesProgress: true,
					paginationClickable: true,
					initialSlide : index,
					pagination : '.swiper-pagination',
					effect : 'coverflow',
					coverflow: {
			            rotate: -12,
			            stretch: 40,
			            depth: 100,
			            modifier: 2,
			            slideShadows : false
			        },
					paginationBulletRender: function (index, className) {
						switch (index) {
							case 0: name='洗';break;
							case 1: name='剪';break;
							case 2: name='烫';break;
							case 3: name='染';break;
							case 4: name='护';break;
							case 5: name='套';break;
							case 6: name='套';break;
							default: name='';
						}
						return '<span class="' + className + '"><i>' + name + '</i></span>';
					},
					onSlideChangeEnd:function(swiper){
						// $(".pic-box").attr("index", swiper.activeIndex);  //取索引值
						// $(".swiper-slide").eq(swiper.activeIndex).addClass("shopCart");
						// $(".character-effect").find(".designer-pic").eq(swiper.activeIndex).removeClass("none").siblings(".designer-pic").addClass("none")
					},
					onProgress: function(swiper){
						for (var i = 0; i < swiper.slides.length; i++){
							var slide = swiper.slides[i];
							var progress = slide.progress;
							swiper.slides[i].style.opacity = 1 - Math.min(Math.abs(progress/2),1);
							swiper.slides[i].style.webkitTransform = 
							swiper.slides[i].style.MsTransform = 
							swiper.slides[i].style.msTransform = 
							swiper.slides[i].style.MozTransform = 
							swiper.slides[i].style.OTransform = 
							swiper.slides[i].style.transform = 'translate3d(0px,0,'+(-Math.abs(progress*150))+'px)'
						}
					},
					onSetTransition: function(swiper, speed) {
						for (var i = 0; i < swiper.slides.length; i++) {
							swiper.slides[i].style.webkitTransitionDuration = 
							swiper.slides[i].style.MsTransitionDuration = 
							swiper.slides[i].style.msTransitionDuration = 
							swiper.slides[i].style.MozTransitionDuration = 
							swiper.slides[i].style.OTransitionDuration = 
							swiper.slides[i].style.transitionDuration = speed + 'ms';
						}

						$(".want-mank").attr("href", "../getavatar/getavatar#" + swiper.activeIndex);  //取索引值
						$(".pic-box").attr("index", swiper.activeIndex);  //取索引值
						$(".swiper-slide").eq(swiper.activeIndex).addClass("shopCart");
						$(".character-effect").find(".designer-pic").eq(swiper.activeIndex).removeClass("none").siblings(".designer-pic").addClass("none");

					}
				});
					
   			},
	        
        });
    });
