include_recipe 'mysql::client'
include_recipe 'mysql::server'
include_recipe "mysql-chef_gem"

mysql_client 'default' do
  action :create
end

mysql_conn_info = {:host => 'localhost', :username => 'root', :password => node['mysql']['server_root_password']};

mysql_database node['mysql']['db'] do
  connection mysql_conn_info
  action :create
end

mysql_database_user node['mysql']['user'] do
  connection mysql_conn_info
  password   node['mysql']['pass']
  action     :create
end

# Let this database user access this database
mysql_database_user node['mysql']['user'] do
  mysql_conn_info
  password      node['mysql']['pass']
  database_name node['mysql']['db']
  host          'localhost'
  privileges    [:select, :update, :insert, :delete, :create, :alter]
  action        :grant
end

# Write schema seed file to filesystem.
cookbook_file node['mysql']['seed_file'] do
  source 'create-tables.sql'
  owner 'root'
  group 'root'
  mode '0600'
end

# Seed the database with a table and test data.
execute 'initialize database' do
  command "mysql -h #{node['mysql']['host']} -u #{node['mysql']['user']} -p#{node['mysql']['pass']} -D #{node['mysql']['db']} < #{node['mysql']['seed_file']}"
  not_if  "mysql -h #{node['mysql']['host']} -u #{node['mysql']['user']} -p#{node['mysql']['pass']} -D #{node['mysql']['db']} -e 'describe trades;'"
end

package "php5-mysql" do
	action :install
end