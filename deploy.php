<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'weibo');

// Project repository
set('repository', 'git@github.com:RealZark/weibo.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);


// Hosts

host('106.15.36.187')
    ->user('deployer')
    ->stage('production')
    ->identityFile('~/.ssh/deployerkey')
    ->set('deploy_path', '/www/wwwroot/weibo');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

task('reload:php-fpm', function () {
    run('sudo /usr/sbin/service php7-fpm reload');
});

desc('Reload php-fpm');
after('deploy', 'reload:php-fpm');

desc('Restart horizon queue');
after('deploy', 'artisan:horizon:terminate');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');
