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

- http://localhost:5601/ - Kibana
- http://localhost:3000/ - Grafana
- http://localhost:9090/ - Prometheus
- http://localhost:8080/ - cAdvisor
- http://localhost:9100/ - NodeExporter
- http://localhost:9093/ - AlertManager
- http://localhost:8088/ - MailCatcher
- http://localhost:7070/ - GitLab
- http://localhost:9099/ - Sphinx Search webproc
- http://magento.local:80/ - Magento frontend (Varnish - nginx - php7-fpm)
- http://magento.local:80/Magento/ - Magento admin

## Todo

- https://github.com/ausger/SphinxSearch
- https://github.com/Doability/magento2dev/tree/552ea4fb32c5314bff48077361e519f633f47b58/vendor/mirasvit

## See other

- https://github.com/uschtwill/docker_monitoring_logging_alerting/blob/master/docker-compose.yml
