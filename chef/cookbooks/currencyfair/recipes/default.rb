#
# Cookbook Name:: currencyfair
# Recipe:: default
#
# Copyright (C) 2015 YOUR_NAME
#
# All rights reserved - Do Not Redistribute
#

group node['currencyfair']['group']

user node['currencyfair']['user'] do
  group node['currencyfair']['group']
  system true
  shell '/bin/bash'
end

include_recipe 'mysql::client'
include_recipe 'mysql::server'
include_recipe 'nginx'
include_recipe 'php'
include_recipe 'php-fpm'
include_recipe 'beanstalkd'