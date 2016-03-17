define(['app/views/base', 'tpl!app/views/index/IndexView.html', 'swiper'],
    function (View, template, Swiper) {
        "use strict";
        
        return View.extend({
            tagName: 'div',
            className: '',
            template: template,
            events: {
            	"click .mongolia-layer": "disappear",
            },
            initialize: function() {
                  this.listenTo(this.model, 'change', this.render);
                  this.model.fetch();
            },
            disappear: function(e) {
            	
            	$(e.currentTarget).addClass("none");
            },
   			onRender: function() {
   				setTimeout(function() {
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
							$(".pic-box").attr("index", swiper.activeIndex);  //取索引值
							$(".swiper-slide").eq(swiper.activeIndex).addClass("shopCart");
						},
						onProgress: function(swiper){
							for (var i = 0; i < swiper.slides.length; i++){
								var slide = swiper.slides[i];
								var progress = slide.progress;
								// scale = 1 - Math.min(Math.abs(progress * 0.2), 1);
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
								// es = swiper.slides[i].style;
								swiper.slides[i].style.webkitTransitionDuration = 
								swiper.slides[i].style.MsTransitionDuration = 
								swiper.slides[i].style.msTransitionDuration = 
								swiper.slides[i].style.MozTransitionDuration = 
								swiper.slides[i].style.OTransitionDuration = 
								swiper.slides[i].style.transitionDuration = speed + 'ms';
							}
						},
					});
					setInterval(function() {
						$(".choice").attr("href", "../selectmale/selectmale#"+ mySwiper.activeIndex);
					},100)
   				},100)

   			},
        });
    });
