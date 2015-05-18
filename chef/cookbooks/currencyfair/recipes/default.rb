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

include_recipe 'php'
include_recipe 'php-fpm'
include_recipe 'nginx'
include_recipe 'beanstalkd'
include_recipe 'composer'

template "#{node.nginx.dir}/sites-available/" + HOSTNAME do
  mode 0644
  owner node.nginx.user
  group node.nginx.user
  variables({
    :name => 'currencyfair',
    :host => HOSTNAME,
    :root => DOCUMENT_ROOT + "/cfweb"
  })
end

template "/etc/init/cfdaemon.conf" do
  mode 0644
  owner 'root'
  group 'root'
  variables({
    :exec_path => DOCUMENT_ROOT + '/cfdaemon/bin/cfdaemon'
  })
end

template DOCUMENT_ROOT + "/cfdaemon/conf/cfdaemon.ini" do
  mode 0644
end

template DOCUMENT_ROOT + "/cfweb/cfweb.ini" do
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

nginx_site HOSTNAME do
  enable true
end

service "php-fpm" do
  notifies :reload, 'service[nginx]'
end

php_pear "System_Daemon" do
  action :install
end

bash "cfdaemon_init" do
  code <<-EOH
    #{DOCUMENT_ROOT}/cfdaemon/cfdinit
  EOH
  creates "/etc/init.d/cfdaemon"
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

service "cfdaemon" do
  provider Chef::Provider::Service::Upstart
  subscribes :restart, resources(:bash => "cfdaemon_init")
  supports :restart => true, :start => true, :stop => true
  action :start
end
