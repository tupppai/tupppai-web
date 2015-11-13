@servers(['web' => 'jq@loiter.us'])

@task('deploy', ['on' => 'web', 'confirm' => false])
    cd /var/www/ps
    git pull origin {{ $branch }}
    php artisan migrate
@endtask
