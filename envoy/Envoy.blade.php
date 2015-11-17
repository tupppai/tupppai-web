@servers(['web-dev' => 'jq@loiter.us'])

@task('deploy', ['on' => 'web-dev', 'confirm' => false])
    cd /var/www/ps
    git pull origin {{ $branch }}
    php artisan migrate
    php artisan db:seed
@endtask
