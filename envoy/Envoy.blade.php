@servers(['web' => 'jq@loiter.us'])

@task('deploy', ['on' => 'web', 'confirm' => true])
    cd /var/www/ps
    git pull origin {{ $branch }}
    php artisan migrate
@endtask
