<?php

namespace Deployer;

require 'vendor/deployer/deployer/recipe/symfony.php';

serverList('app/config/deployer/servers.yml');

set('bin_dir', 'bin');
set('default_stage', 'staging');

set('repository', 'git@github.com:4xxi/keys.git');
set('keep_releases', 4);

set('http_user', 'www-data');

set('shared_dirs', ['var/logs',]);
set('shared_files', ['app/config/parameters.yml']);
set('writable_dirs', ['var/cache', 'var/logs', 'var/sessions']);
set('assets', ['web/css', 'web/images', 'web/js']);

task('reload:php-fpm', function () {
    run('sudo service php7.1-fpm reload');
});

after('deploy:symlink', 'database:migrate');
after('deploy', 'reload:php-fpm');
after('rollback', 'reload:php-fpm');
