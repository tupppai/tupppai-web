run:
	nohup php artisan queue:listen > daemons/nohup.out & 
stop:
	pkill php
# 需要先安装envoy
deploy: 
	cd daemons/envoy; ~/.composer/vendor/bin/envoy run deploy; cd ../..
package: 
	cd daemons/envoy; ~/.composer/vendor/bin/envoy run package; cd ../..
release:
	cd daemons/envoy; ~/.composer/vendor/bin/envoy run release; cd ../..
