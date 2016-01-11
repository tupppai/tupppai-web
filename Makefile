# 需要先安装envoy
deploy: 
	git checkout develop
	git checkout public/index.html
	git pull origin master
	git pull origin frontend
	git pull origin develop
	date > public/src/dist/readme.md
	echo '如果有冲突文件请解决'
	rm -rf public/src/dist; cd public/src; gulp app; gulp less; gulp cp; 
	php public/src/index.php local > public/index.html
	#rm -rf public/src/dist; cd public/src; gulp app; gulp less; gulp rjs; gulp cp;
	#php public/src/index.php production > public/index.html
	cd ../.. ; 
	git add public/index.html; git add public/src/dist ; git commit -m 'deploy dist'; git push origin develop ;
	cd tools/envoy; ~/.composer/vendor/bin/envoy run web-deploy; cd ../.. ;
publish: 
	echo '需要在使用make deploy，于测试环境测试通过之后方可发布现网'
	git checkout master
	git checkout public/index.html
	git pull origin master
	rm -rf public/src/dist; cd public/src; gulp app; gulp less; gulp rjs; gulp cp
	cd ../..
	date > public/src/dist/readme.md
	php public/src/index.php production > public/index.html;
	git add public/index.html; 
	git add public/src/dist
	git commit -m 'publish dist'
	git push origin master
	git push destination master
	cd tools/envoy; ~/.composer/vendor/bin/envoy run web-publish; cd ../.. ;
build:
	php public/src/index.php production > public/index.html ;
	rm -rf public/src/dist; cd public/src; gulp app; gulp less; gulp rjs; gulp cp
watch:
	php public/src/index.php local > public/index.html ;
	rm -rf public/res; rm -rf public/css; cd public/src; gulp app; gulp less; gulp watch
package: 
	cd tools/envoy; ~/.composer/vendor/bin/envoy run android-package; cd ../..
release:
	#cd /Users/junqiang/www/tupppai-android
	#git pull origin release
	#./gradlew assembleRelease -Pandroid.injected.signing.store.file={{$keystore}} -Pandroid.injected.signing.store.password={{$keyPwd}} -Pandroid.injected.signing.key.alias={{$keyAlias}} -Pandroid.injected.signing.key.password={{$keyPwd}}
	cd tools/envoy; ~/.composer/vendor/bin/envoy run android-release; cd ../..
	open /Users/junqiang/www/tupppai-android/appStartActivity/build/outputs/apk
#setup:
	#alias proxychains4='proxychains4 -f ~/.proxychans/proxychains.conf'
	#export PATH=/opt/local/bin:$PATH
run:
	sh tools/supervisor/supervisor.sh start
install:
	if [ ! -d "/data " ]; then
		sudo mkdir /data
		cd /data
		sudo chmod -R 777 .
		git clone git@github.com:whenjonny/tupppai-storage.git storage
		sudo chmod -R 777 .
	fi
