# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"
PUBLIC_NETWORK_IP = "192.168.0.99"
PRIVATE_NETWORK_IP = "192.168.10.10"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.define 'local' do |local|
  
    local.vm.box = "hashicorp/precise32"
    local.vm.hostname = "currencyfair.lan"

    local.berkshelf.enabled = true
    local.berkshelf.berksfile_path = 'chef/cookbooks/currencyfair/Berksfile'

    local.vm.network "private_network", ip: PRIVATE_NETWORK_IP
    local.vm.network "public_network", ip: PUBLIC_NETWORK_IP

    local.vm.synced_folder "src/", "/srv/www/" + local.vm.hostname

    local.hostmanager.enabled = true
    local.hostmanager.manage_host = true

    local.vm.provider "virtualbox" do |vb|
      vb.gui = false
      vb.memory = "1024"
    end

    local.vm.provision "chef_solo" do |chef|
      chef.node_name = "currencyfair"
      chef.cookbooks_path = "chef/cookbooks"
      chef.roles_path = "chef/roles"
      chef.add_role("web")
      chef.json = {
        "currencyfair" => {
          "domain" => local.vm.hostname
        }
      }
    end
  end

  config.vm.define 'remote' do |remote|
    remote.vm.box = "digital_ocean"
    remote.vm.hostname = "currencyfair.bunsfamily.com"

    remote.ssh.private_key_path = "~/.ssh/id_rsa_vagrant"
    remote.vm.provider :digital_ocean do |provider|
      provider.token = "<your_digitalocean_token_here>"
      provider.image = "ubuntu-12-04-x32"
      provider.region = "nyc2"
    end

    remote.berkshelf.enabled = true
    remote.berkshelf.berksfile_path = 'chef/cookbooks/currencyfair/Berksfile'

    remote.vm.synced_folder "src/", "/srv/www/" + remote.vm.hostname

    remote.hostmanager.enabled = true
    remote.hostmanager.manage_host = true

    remote.vm.provision "chef_solo" do |chef|
      chef.node_name = "currencyfair"
      chef.cookbooks_path = "chef/cookbooks"
      chef.roles_path = "chef/roles"
      chef.add_role("web")

      chef.json = {
        "currencyfair" => {
          "domain" => remote.vm.hostname
        }
      }
    end
  end
end
