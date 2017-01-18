# magento-docker

## Installing

```
git clone http://gitlab:9090/dynamite/magento-docker.git
cd magento-docker
make rebuild && make
```

## Running

```
# start / restart
make

# stop
make stop
```

## Links

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

## Passwords

* Magento admin
  * username: root
  * password: 1234abcd
* Grafana
  * username: admin
  * password: admin

## Todo

- https://github.com/ausger/SphinxSearch
- https://github.com/Doability/magento2dev/tree/552ea4fb32c5314bff48077361e519f633f47b58/vendor/mirasvit

## See other

- https://github.com/uschtwill/docker_monitoring_logging_alerting/blob/master/docker-compose.yml
