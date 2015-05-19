#CurrencyFair

My 50 cents to job applications parade on Github ^_^

API Endpoint testing URL: http://currencyfair.bunsfamily.com/trade

Project view with realtime updates: http://currencyfair.bunsfamily.com

##General Idea
From the top my vision general idea of CurrencyFair project is somewhere between deep analysis of market trends and lightning-fast reaction to any user needs. And as soon as test application is focused on API endpoint - i decide to create proof of concept how it will work on top of general unix-way style. Tiny tools connected to each other with some kind of pipes and completely independent. So, while some component can cause problems to whole project idea - at the same time any sunk component will not destory everythnig else in the chain and can be repaired fairly quicky because of it's size and higly focused target area.
##Requirements

###Software
  - Vagrant
  - Chef
  - Beanstalkd
  - MySQL (just persistent storage and nothing else)
  - Memcached
  - Nginx
  - Php-fpm

###Libraries / Components
   - Slim (API endpoint and Web view)
   - Raphael (Amazing SVG swiss knife, used for global map)
   - PEAR_System_Daemon (old school is in our blood, yeah)
   - PHPUnit / guzzle (mostly for API endpoint unit test)
   - Pheanstalk (PHP lib to talk with Beanstalkd queue's)
   - Wrench (PHP realization of WebSockets server, fast and easy to work with)
    
##Install
Everything you need to run this project is vagrant and list of plugins for it:
   - vagrant-berkshelf (for chef cookbooks dependencies, see more at http://berkshelf.com/)
   - vagrant-digitalocean (easy deploy out from the box)
   - vagrant-hostmanager (managing hosts file without headeaches)

All this stuff can be easily installed with next commands:
```sh
$ vagrant plugin install <plugin_name_here>
```
After this is done - just clone repo and run
```sh
$ vagrant up local
```
If everythnig will goes fine - in few minutes you will receive working demo at http://currencyfair.lan address.

I decide to deploy my test app for review and testing to DigitalOcean, so if you wanna try this path - just fix `provider.token` parameter in remote vm section of `Vagrantfile` and run 
```
vagrant up remote --provider=digital-oceam
```
##Parts explained
From this point and to the end of readme I'm assuming that everything is checked on local VM install.
###Message Consumer
Consuming interface located at http://currencyfair.lan/trade, builded on top of Slim framework and can be reviewed in `src/web` folder of this repo.

Nothing special - simple parameters check and pushing whole json to beanstalk for next tools in the tube. Blazing fast and easy.

###Message Processor
Located in `src/cfdaemon` path. This is tiny PHP-based daemon, who constantly watch Beanstalkd tube and react on messages passed to it. After receiving new JSON from beanstalkd cfdaemon put previously filtered in consumer message to MySQL and do quick analysis for originating country, updates country data in memcached storage and pass message to the next pipe into Wrench daemon for WebSockets pass.

###WebSockets interface
This part can be reviewed in `src/cfsocket` folder and on main page http://currencyfair.lan

WebSockets interface combinedfrom 2 parts.

1. Web part with world map (simple Slim-based page and little chunk of JS to handle connections to Wrench daemon), where any new trade will blink on originating country and while mouse pointer hovered some country - basic info with little trade analysis from memcache will be displayed.
2. Daemon part, where Wrench plays. 2 applications connected to Wrench routes - one of apps responsive to pushing messages about country updates to Web view, and second one just return back analysis data from memcached by client's websocket request.

###Testing
Not much done here, but i've created some basic tests to check API and for now tests are passing.

###Performance and Scalability
As you see - any component of the app can be easyly modified and upgraded to use more complex structure of horizontal scaling, even frontend parts of beanstalkd queue. This is not and app in the nutshell, so, I think, if performance requirement not fit already - i'm not so far from this.

###Security
While API endpoint security is all about data filtering - all other components can be, and absolutely must be hided behind some kind of filtering proxy like nginx or haproxy and configured in better way compared to my to maintain all possible breaches. I'll try to do all what i can here, but i think my knowledge in devops field is little bit lower compared to old gurus ^_^

##Disclaimer
This is just a proof of concept. Have fun and hack around.
Still some parts can be much improved, code duplication can be reduced by organizing of shared library between components and so on, but my goal was to make somethnig different and working in end place.

Hope all you guys will like it ^_^
