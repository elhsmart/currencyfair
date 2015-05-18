name             'currencyfair'
maintainer       'Ed "elhsmart" Tretyakov'
maintainer_email 'elhsmart@gmail.com'
license          'All rights reserved'
description      'Installs/Configures currencyfair.com env for test application'
long_description 'Installs/Configures currencyfair'
version          '0.1.0'

depends "database", "= 2.3.1"
depends "mysql-chef_gem", "~> 0.0"
depends "mysql", "~> 5.0"
depends "nginx"
depends "beanstalkd"
depends "php-fpm"
depends "php"
depends "memcached"
depends "composer"