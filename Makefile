# 需要先安装envoy
deploy: 
	cd tools/envoy; ~/.composer/vendor/bin/envoy run deploy; cd ../..
package: 
	cd tools/envoy; ~/.composer/vendor/bin/envoy run package; cd ../..
release:
	cd tools/envoy; ~/.composer/vendor/bin/envoy run release; cd ../..
#setup:
	#alias proxychains4='proxychains4 -f ~/.proxychans/proxychains.conf'
	#export PATH=/opt/local/bin:$PATH
run:
	sh tools/supervisor/supervisor.sh start
build:
	rm -rf public/res; cd public/src; gulp app; gulp css
watch:
	rm -rf public/res; cd public/src; gulp app; gulp watch
