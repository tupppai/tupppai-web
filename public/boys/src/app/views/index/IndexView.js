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
   					 this.model.fetch({
   					 	 success:function(res) {
		                    var code = res.get('code');
		                    alert( code );
		                    if(code == 1) {
		                        location.href = 'http://' + location.hostname + '/boys/uploadsuccess/uploadsuccess';
		                    } 
		                    //求P成功有作品
		                    if(code == 2) {
		                        location.href = 'http://' + location.hostname + '/boys/obtainsuccess/obtainsuccess';
		                    } 
		                    //求P被拒绝
		                    if(code == -1) {
		                        location.href = 'http://' + location.hostname + '/boys/uploadagain/uploadagain';
		                    } 
		                }
   					 });
                    //求P成功 没有作品也没有被拒绝
            },
            disappear: function(e) {
            	$(e.currentTarget).addClass("none");
            },
   			onShow: function() {
   			
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
						$(".pic-box").attr("index", swiper.activeIndex);  //取索引值
						$(".swiper-slide").eq(swiper.activeIndex).addClass("shopCart");
						$(".choice").attr("href", "../selectmale/selectmale#"+ swiper.activeIndex);
					},
				});

				// 微信好友文案修改
                var options = {};
                options.code = $('body').attr('data-code');
                share_friend(options,function(){},function(){})
                share_friend_circle(options,function(){},function(){})
   			},
        });
    });
