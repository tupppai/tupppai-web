define(['app/views/base', 'tpl!app/views/index/IndexView.html'],
    function (View, template) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events:{
            	'click #select_boy' : "selectBoy"
            },
            selectBoy:function() {
            	wx_choose_image();
            },
   			onRender: function() {
				var mySwiper = new Swiper('.swiper-container',{
					slidesPerView : 'auto',
					centeredSlides : true,
					watchSlidesProgress: true,
					paginationClickable: true,
					initialSlide :3,
					pagination : '.swiper-pagination',
					effect : 'coverflow',
					coverflow: {
			            rotate: -12,
			            stretch: 50,
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
					$(".pic-box").attr("index", swiper.activeIndex);  //取索引值
				},
				onProgress: function(swiper){
					for (var i = 0; i < swiper.slides.length; i++){
						var slide = swiper.slides[i];
						var progress = slide.progress;
						// scale = 1 - Math.min(Math.abs(progress * 0.2), 1);

						es = slide.style;
						es.opacity = 1 - Math.min(Math.abs(progress/2),1);
						es.webkitTransform = 
						es.MsTransform = 
						es.msTransform = 
						es.MozTransform = 
						es.OTransform = 
						es.transform = 'translate3d(0px,0,'+(-Math.abs(progress*150))+'px)'

					}
				},

				onSetTransition: function(swiper, speed) {
					for (var i = 0; i < swiper.slides.length; i++) {
						es = swiper.slides[i].style;
						es.webkitTransitionDuration = 
						es.MsTransitionDuration = 
						es.msTransitionDuration = 
						es.MozTransitionDuration = 
						es.OTransitionDuration = 
						es.transitionDuration = speed + 'ms';
					}
				}
				});

				$(".designer-pic img").click(function(e) {
					var index = mySwiper.activeIndex;  //获取当前滑块的索引值
					var src = $(this).attr("src"); //获取点击图片的src
					$(".main-img").eq(index).attr("src", src);
				})
   			},
        });
    });
