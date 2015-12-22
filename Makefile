# 需要先安装envoy
deploy: 
	cd tools/envoy; ~/.composer/vendor/bin/envoy run web-deploy; cd ../..
package: 
	cd tools/envoy; ~/.composer/vendor/bin/envoy run android-package; cd ../..
release:
	cd tools/envoy; ~/.composer/vendor/bin/envoy run android-release; cd ../..
publish: 
	cd tools/envoy; ~/.composer/vendor/bin/envoy run web-publish; cd ../..
#setup:
	#alias proxychains4='proxychains4 -f ~/.proxychans/proxychains.conf'
	#export PATH=/opt/local/bin:$PATH
run:
	sh tools/supervisor/supervisor.sh start
build:
	rm -rf public/res; cd public/src; gulp app; gulp css; gulp release
watch:
	rm -rf public/res; cd public/src; gulp app; gulp watch
