#
# Cookbook Name:: currencyfair
# Recipe:: default
#
# Copyright (C) 2015 YOUR_NAME
#
# All rights reserved - Do Not Redistribute
#
HOSTNAME = "currencyfair.lan"
DOCUMENT_ROOT = "/srv/www/" + HOSTNAME

group node['currencyfair']['group']

user node['currencyfair']['user'] do
  group node['currencyfair']['group']
  system true
  shell '/bin/bash'
end

include_recipe 'mysql::client'
include_recipe 'mysql::server'
include_recipe 'php'
include_recipe 'php-fpm'
include_recipe 'nginx'
include_recipe 'beanstalkd'

template "#{node.nginx.dir}/sites-available/" + HOSTNAME do
  mode 0644
  owner node.nginx.user
  group node.nginx.user
  variables({
    :name => 'currencyfair',
    :host => HOSTNAME,
    :root => DOCUMENT_ROOT
  })
end

nginx_site 'default' do
  enable false
end

path_chunks = DOCUMENT_ROOT.split("/")
path = ""

path_chunks.each do |chunk|
  path = path + "/" + chunk
  if !File.directory?(path)
    directory path do
      owner node.nginx.user
      group node.nginx.user
      mode '0755'
      action :create
    end
  end
end

nginx_site HOSTNAME do
  enable true
end

service "php-fpm" do
  notifies :reload, 'service[nginx]'
end