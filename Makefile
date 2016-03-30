# 需要先安装envoy
deploy: 
	git checkout develop
	git checkout public/index.html
	git pull origin master
	git pull origin frontend
	git pull origin develop
	echo '如果有冲突文件请解决'
	rm -rf public/css;
	# add main & boy
	cd public/boys/src; gulp build; cd ../../.. ; 
	cd public/main/src; gulp build; cd ../../.. ; 
	git add public/css;
	git add public/boys/index.html
	git add public/main/index.html
	git commit -m 'deploy dist'; git push origin develop ;
	# deploy
	cd /data/tools/envoy; ~/.composer/vendor/bin/envoy run web-deploy; cd -;
publish: 
	echo '需要在使用make deploy，于测试环境测试通过之后方可发布现网'
	git checkout master
	git checkout public/index.html
	git pull origin master
	rm -rf public/css;
	cd public/boys/src; gulp release; cd ../../.. ; 
	cd public/main/src; gulp release; cd ../../.. ; 
	git add public/css;
	git add public/boys/index.html;
	git add public/main/index.html;
	git commit -m 'publish dist'
	git push origin master
	git push destination master
	cd /data/tools/envoy; ~/.composer/vendor/bin/envoy run web-publish; cd - ;
build:
	cd public/boys/src; gulp build; cd ../../.. ; 
	cd public/main/src; gulp build; cd ../../.. ; 
package:
	cd /data/tools/envoy; ~/.composer/vendor/bin/envoy run android-package; cd -
release:
	cd /data/tools/envoy; ~/.composer/vendor/bin/envoy run android-release; cd -
	open /Users/junqiang/www/tupppai-android/appStartActivity/build/outputs/apk
#setup:
	#alias proxychains4='proxychains4 -f ~/.proxychans/proxychains.conf'
	#export PATH=/opt/local/bin:$PATH
listen:
	cd public/src; gulp listen
run:
	if test -d "/data/tools" ; \
	then echo 'success';  \
	else \
	echo 'please install tools'; \
	cd /data/; sudo git clone git@github.com:whenjonny/tupppai-tools.git tools ; \
	fi ;
	if test -d "/data/storage" ; \
	then echo 'success';  \
	else echo 'please install tools'; \
	cd /data/; sudo git clone git@github.com:whenjonny/tupppai-storage.git storage;  \
	fi ;
	/bin/sh /data/tools/supervisor/supervisor.sh start ;
