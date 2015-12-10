@servers([ 'web-dev' => 'jq@loiter.us', 'apk-dev' => '127.0.0.1', 'apk-production' => '127.0.0.1', 'web-production1' => 'ubuntu@www.tupppai.com', 'web-production2' => 'ubuntu@www.tupppai.com'])

@task('deploy', ['on' => 'web-dev', 'confirm' => false])
    cd /var/www/ps
    git pull origin develop
    php artisan migrate
    php artisan db:seed
@endtask

@task('release', ['on' => 'apk-production', 'confirm' => false])
    cd /Users/junqiang/www/tupppai-android
    git pull origin master
    ./gradlew assembleRelease -Pandroid.injected.signing.store.file=/Users/junqiang/.gradle/keystore -Pandroid.injected.signing.store.password=psgod1234 -Pandroid.injected.signing.key.alias=psgod -Pandroid.injected.signing.key.password=psgod1234
@endtask

@task('package', ['on' => 'apk-dev', 'confirm' => false])
    curl http://admin.loiter.us/push/fetchApk > /tmp/apk.exist
    cat /tmp/apk.exist | while read line
    do
        echo "$line"
        if [ "$line" = 1 ]; then
            echo 'remove all history apks'
            rm -rf /Users/junqiang/www/tupppai-android/appStartActivity/build/outputs/apk/*
            echo begin build apk
            cd /Users/junqiang/www/tupppai-android
            git pull origin master
            ./gradlew assembleUmengRelease -Pandroid.injected.signing.store.file=/Users/junqiang/.gradle/keystore -Pandroid.injected.signing.store.password=psgod1234 -Pandroid.injected.signing.key.alias=psgod -Pandroid.injected.signing.key.password=psgod1234
            #./gradlew assembleUmengRelease && curl http://admin.loiter.us/push/mailApk
            scp /Users/junqiang/www/tupppai-android/appStartActivity/build/outputs/apk/tupppai_v1.0.4_umeng.apk jq@loiter.us:/var/www/ps/public/mobile/apk/tupai.apk
            curl http://admin.loiter.us/push/mailApk
        else
            echo done
        fi
    done
@endtask
