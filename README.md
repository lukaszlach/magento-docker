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
- http://localhost:9093/ - AlertManager
- http://localhost:7070/ - GitLab
- http://magento.local:80/ - Magento frontend
- http://magento.local:80/Magento/ - Magento admin

## See other

- https://github.com/uschtwill/docker_monitoring_logging_alerting/blob/master/docker-compose.yml
