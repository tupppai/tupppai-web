# 需要先安装envoy
deploy: 
	git checkout develop
	git pull origin master
	git pull origin frontend
	git pull origin develop
	echo '如果有冲突文件请解决'
	rm -rf public/src/dist; cd public/src; gulp app; gulp less; gulp cp; 
	cd ../.. ; 
	git add public/src/dist ; git commit -m 'deploy dist'; git push origin develop ;
	cd tools/envoy; ~/.composer/vendor/bin/envoy run web-deploy; cd ../..
publish: 
	git pull origin master
	rm -rf public/src/dist; cd public/src; gulp app; gulp less; gulp rjs; gulp cp
	cd ../..
	git add public/src/dist
	git commit -m 'publish dist'
	git push origin master
	git push destination master
	cd tools/envoy; ~/.composer/vendor/bin/envoy run web-publish; cd ../..
build:
	rm -rf public/src/dist; cd public/src; gulp app; gulp less; gulp rjs; gulp cp
watch:
	rm -rf public/res; rm -rf public/css; cd public/src; gulp app; gulp less; gulp watch
package: 
	cd tools/envoy; ~/.composer/vendor/bin/envoy run android-package; cd ../..
release:
	cd tools/envoy; ~/.composer/vendor/bin/envoy run android-release; cd ../..
#setup:
	#alias proxychains4='proxychains4 -f ~/.proxychans/proxychains.conf'
	#export PATH=/opt/local/bin:$PATH
run:
	sh tools/supervisor/supervisor.sh start
