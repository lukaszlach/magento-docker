# magento-docker

&copy; 2017 Łukasz Lach

Bootstrap Magento 1.9 or 2.1 from scratch using Docker. Starts up 22 containers, all integrated together with Magento and supporting logging, monitoring, alerting and graphing.

This repository is meant to be a start for a fresh Magento project. After bootstrap is done, you can initialize git repository under `www/` directory and proceed with your project.

Magento is installed using files from `install/` directory that contains compressed source code for both versions, replace it with your file to use other.

Magento is pre-configured with following settings:

 * session storage is using Memcached server
 * cache and page-cache is using Redis server
 * mail settings are pointing MailCatcher

## Containers

Magento is installed on `magento.local` hostname for both versions supported. To access this hostname locally you will need to modify your `/etc/hosts` file so that `localhost` line looks like this:

```
127.0.0.1 localhost magento.local
```

Other containers are available on `localhost`, replace it with domain you will be running project on, if needed.

### Mail

- http://localhost:6009/ - MailCatcher

### Logging

- http://localhost:6008/ - Kibana

### Monitoring

- http://localhost:6001/ - NodeExporter
- http://localhost:6002/ - cAdvisor
- http://localhost:6003/ - Prometheus
- http://localhost:6004/ - Grafana
- http://localhost:6005/ - AlertManager
- http://localhost:6006/ - Portainer
- http://localhost:6007/ - Sphinx Search webproc

### Repository

- http://localhost:7070/ - GitLab

### Project

- http://magento.local:80/ - Magento frontend (Varnish -> nginx -> php7-fpm)
- http://magento.local:80/Magento/ - Magento 2.1 admin
- http://magento.local:80/admin/ - Magento 1.9 admin

## Running

To start a new project based on Magento 2.1 run:

```
git clone https://github.com/lukaszlach/magento-docker.git
cd magento-docker/
VERSION=2.1 make rebuild
```

To use the old 1.9 branch run:

```
git clone https://github.com/lukaszlach/magento-docker.git
cd magento-docker/
VERSION=1.9 make rebuild
```

These commands will install and pre-configure Magento instance. After all steps are done you should be able to access `http://magento.local` in your web browser, as well as all other containers listening on HTTP ports listed above.

> Keep in mind to run `rebuild` once only when starting new project, otherwise all your data will be lost.

To stop all containers run:

```
make stop
```

To start existing project and all dependent containers run:

```
make start
```

## Maintenance

* `make php_cli` - Bash for PHP container
* `make varnish_cli`
* `make nginx_cli`
* `make sphinx_cli`
* `make mysql_cmd` - MySQL command-line interface
* `make sphinx_cmd` - Sphinx command-line interface

There are several tools available inside `php` container, including:

* `n98-magerun` - for Magento 1.9
* `n98-magerun2` - for Magento 2.1
* `phpcpd` - PHP Copy/Paste Detector
* `pdepend` - PHP Depend
* `phpmd` - PHP Mess Detector
* `phpunit` - PHPUnit
* `phpcs` - PHP_CodeSniffer
* `composer`
* `modman`
* `phpdoc` - phpDocumentor
* `phploc` - measure the size of a PHP project

To use tools, after running containers, execute either:

```
make php_cli
phploc /srv/www
n98-magerun2 sys:info # for Magento 2.1
n98-magerun sys:info # for Magento 1.9
```

or

```
docker exec php phploc /srv/www
docker exec php n98-magerun2 sys:info
```

## Default passwords

* Magento admin
  * username: root
  * password: 1234abcd

* Grafana
  * username: admin
  * password: admin

## Known limitations

* locale and currency must be set by editing proper `assets/` script, this will be moved to variables when calling `make rebuild`

## Copyright and License (BSD 2-clause)

Copyright (c) 2017, Łukasz Lach
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

## See also

* https://github.com/kojiromike/docker-magento/tree/master/tools