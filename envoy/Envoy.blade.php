@servers(['web-dev' => 'jq@loiter.us', 'apk-dev' => '127.0.0.1'])

@task('deploy', ['on' => 'web-dev', 'confirm' => false])
    cd /var/www/ps
    git pull origin master
    php artisan migrate
    php artisan db:seed
@endtask

@task('release', ['on' => 'apk-dev', 'confirm' => false])
    cd /Users/junqiang/www/psgod-android-as
    git pull origin master
    ./gradlew assembleUmengRelease && php artisan mail-apk
@endtask
