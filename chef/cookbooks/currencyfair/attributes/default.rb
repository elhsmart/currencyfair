default['currencyfair']['user'] = 'currencyfair'
default['currencyfair']['group'] = 'currencyfair'
default['currencyfair']['domain'] = 'currencyfair.lan'

default['beanstalkd']['opts']['l'] = '127.0.0.1'
default['beanstalkd']['opts']['p'] = '11300'

default['app']['cfdaemon_tube'] = 'currencyfair_cfdaemon'
default['app']['timeout'] = 5

default['app']['cfsocket_tube'] = 'currencyfair_cfsocket'

default['mysql']['db'] = 'app';
default['mysql']['host'] = 'localhost';
default['mysql']['user'] = default['currencyfair']['user']
default['mysql']['pass'] = 'some_user_password'
default['mysql']['seed_file'] ='/tmp/create-tables.sql'

default['memcache']['host'] = '127.0.0.1'
default['memcache']['port'] = '11211'

default['wrench']['host'] = '0.0.0.0'
default['wrench']['port'] = '8080'
