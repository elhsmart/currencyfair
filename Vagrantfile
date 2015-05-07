# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"
PUBLIC_NETWORK_IP = "192.168.0.99"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "hashicorp/precise32"
  config.vm.hostname = "currencyfair"

  config.berkshelf.enabled = true
  config.berkshelf.berksfile_path = 'chef/cookbooks/currencyfair/Berksfile'

  config.vm.network "private_network", ip: "192.168.1.10"
  config.vm.network "public_network", ip: PUBLIC_NETWORK_IP

  config.vm.provider "virtualbox" do |vb|
    vb.gui = false
    vb.memory = "1024"
  end

  config.vm.provision "chef_solo" do |chef|
    chef.node_name = "currencyfair"
    chef.cookbooks_path = "chef/cookbooks"
    chef.roles_path = "chef/roles"
    chef.add_role("web")
  end
end
