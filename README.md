# magento-docker

Bootstrap Magento 1.9 or 2.1 from scratch using Docker. Starts up 22 container, all integrated together with Magento and supporting logging, monitoring and graphing.

This repository is meant to be a start for a fresh Magento project. After bootstrap is done, you can initialize git repository under `www/` directory and proceed with your project.

Magento is pre-configured with following settings:

 * session storage is using Memcached server
 * cache and page-cache is using Redis server
 * mail settings are pointing MailCatcher

## Containers

Magento is installed using `magento.local` hostname for both Magento versions supported. To access this hostname locally you will need to modify your `/etc/hosts` file so that `localhost` like looks like this:

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

- http://magento.local:80/ - Magento frontend (Varnish - nginx - php7-fpm)
- http://magento.local:80/Magento/ - Magento 2.1 admin
- http://magento.local:80/admin/ - Magento 1.9 admin

## Running

To start a new project based on Magento 2.1 run:

```
git clone https://github.com/lukaszlach/magento-docker.git
cd magento-docker/
VERSION=2.1 make rebuild
```

To use the old 1.9 brach run:

```
git clone https://github.com/lukaszlach/magento-docker.git
cd magento-docker/
VERSION=1.9 make rebuild
```

After all steps are done you should be able to access `http://magento.local` in your web browser, as well as all other containers listening on ports listed above. Keep in mind to run this once only when starting new project, otherwise all your data will be lost.

To stop all containers run:

```
make stop
```

To start existing project and all dependent containers run:

```
make start
```

## Default passwords

* Magento admin
  * username: root
  * password: 1234abcd

* Grafana
  * username: admin
  * password: admin