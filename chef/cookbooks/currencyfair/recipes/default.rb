#
# Cookbook Name:: currencyfair
# Recipe:: default
#
# Copyright (C) 2015 YOUR_NAME
#
# All rights reserved - Do Not Redistribute
#
HOSTNAME = node['currencyfair']['domain']
DOCUMENT_ROOT = "/srv/www/" + HOSTNAME

group node['currencyfair']['group']

user node['currencyfair']['user'] do
  group node['currencyfair']['group']
  system true
  shell '/bin/bash'
end

include_recipe 'php'
include_recipe 'php-fpm'
include_recipe 'php::module_curl'
include_recipe 'nginx'
include_recipe 'beanstalkd'
include_recipe 'memcached'
include_recipe 'composer'

package "php5-memcache" do
  action :install
end

template "#{node.nginx.dir}/sites-available/currencyfair.host" do
  mode 0644
  owner node.nginx.user
  group node.nginx.user
  variables({
    :name => 'currencyfair',
    :host => HOSTNAME,
    :root => DOCUMENT_ROOT + "/cfweb"
  })
end

composer_project DOCUMENT_ROOT + "/cfdaemon" do
    dev false
    quiet true
    action :install
end

composer_project DOCUMENT_ROOT + "/cfweb" do
    dev false
    quiet true
    action :install
end

composer_project DOCUMENT_ROOT + "/cfsocket" do
    dev false
    quiet true
    action :install
end

composer_project DOCUMENT_ROOT + "/test" do
    dev false
    quiet true
    action :install
end

template "/etc/init/cfdaemon.conf" do
  mode 0644
  owner 'root'
  group 'root'
  variables({
    :exec_path => DOCUMENT_ROOT + '/cfdaemon/bin/cfdaemon'
  })
end

template "/etc/init/cfsocket.conf" do
  mode 0644
  owner 'root'
  group 'root'
  variables({
    :exec_path => DOCUMENT_ROOT + '/cfsocket/bin/cfsocket'
  })
end

template DOCUMENT_ROOT + "/cfdaemon/conf/cfdaemon.ini" do
  mode 0644
end

template DOCUMENT_ROOT + "/cfsocket/conf/cfsocket.ini" do
  mode 0644
end

template DOCUMENT_ROOT + "/cfweb/conf/cfweb.ini" do
  mode 0644
end

template DOCUMENT_ROOT + "/test/conf/cftest.ini" do
  mode 0644
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

nginx_site 'currencyfair.host' do
  enable true
end

service "php-fpm" do
  notifies :reload, 'service[nginx]'
end

bash "cfdaemon_init" do
  code <<-EOH
    #{DOCUMENT_ROOT}/cfdaemon/bin/cfdinit
  EOH
  creates "/etc/init.d/cfdaemon"
end

bash "cfsocket_init" do
  code <<-EOH
    #{DOCUMENT_ROOT}/cfsocket/bin/cfinit
  EOH
  creates "/etc/init.d/cfsocket"
end

service "cfdaemon" do
  provider Chef::Provider::Service::Upstart
  subscribes :restart, resources(:bash => "cfdaemon_init")
  supports :restart => true, :start => true, :stop => true
  action :restart
end

service "cfsocket" do
  provider Chef::Provider::Service::Upstart
  subscribes :restart, resources(:bash => "cfsocket_init")
  supports :restart => true, :start => true, :stop => true
  action :restart
end
