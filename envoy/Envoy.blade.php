@servers(['web-dev' => 'jq@loiter.us', 'apk-dev' => '127.0.0.1', 'apk-production' => '127.0.0.1'])

@task('deploy', ['on' => 'web-dev', 'confirm' => false])
    cd /var/www/ps
    git pull origin master
    php artisan migrate
    php artisan db:seed
@endtask

@task('package', ['on' => 'apk-dev', 'confirm' => false])
    cd /Users/junqiang/www/psgod-android-as
    git pull origin master
    ./gradlew assembleUmengRelease && curl http://admin.loiter.us/push/mailApk
    scp /Users/junqiang/www/psgod-android-as/appStartActivity/build/outputs/apk/appStartActivity-umeng-release-unsigned.apk jq@loiter.us:/var/www/ps/public/mobile/apk/tupai.apk
@endtask

@task('release', ['on' => 'apk-production', 'confirm' => false])
    cd /Users/junqiang/www/psgod-android-as
    git pull origin master
    ./gradlew assembleRelease
@endtask
