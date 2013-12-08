CacoCloud
=

A simple, fast and secure PHP/AngularJS based single user feed and mail reader, password and bookmark manager.
CacoCloud is divided into a RESTful PHP backend storing all data into a [SQLite](http://www.sqlite.org/) database and an SPA frontend based on [AngularJs](http://angularjs.org/). 

I have mainly written CacoCloud to fit my personal needs, but maybe it fits your needs, too.


Installation
===
On a *nix machine you can run the `build.sh` script. 
This script will install the needed [node.js](http://nodejs.org/) modules for running grunt, installs [Composer](http://getcomposer.org/) and let the compser install all required PHP libs and creates a new and empty database. It also lets you create new user account.

If you are planning to use the password manager component and using [Apache](http://httpd.apache.org/) you should modify log format, take the example virtual host file from `config/vhost.cfg`! And don't forget to use https instead of http!


Used components
===
CacoCloud is based on some awesome open source libraries and frameworks.

The RESTful backend is based on [Slim PHP](http://www.slimframework.com/), rss/atom feeds gets parsed by [SimplePie](http://simplepie.org/) and E-Mail are send through SMTP by [PHPMailer](http://simplepie.org/).

Since CacoCloud is an SPA, the frontend is build with love, [AngularJs](http://angularjs.org/) and [AngularUI Router](https://github.com/angular-ui/ui-router). Password stored into the password manager gets encrypted by [crypto-js](https://code.google.com/p/crypto-js/) on the client (no fear they are encrypted a second time by [Mcrypt](http://php.net/mcrypt) before they are stored into the database on the server). [zxcvbn](https://github.com/lowe/zxcvbn) estimates the strength of you password before storing it into the password manager.

Thanks to [Bootstrap 3.0](http://getbootstrap.com/) the frontend is clean and responsible and a nice theme from [Bootswatch](http://bootswatch.com/) lets it not looks so Bootstrapped. The icons are by [Font Awesome](http://fontawesome.io/).

At last all frontend code gets minified with the use of [Grunt](http://gruntjs.com/). 

