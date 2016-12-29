default: restart

rebuild: show_rebuild_warning stop build start composer wait_10 magento_install start

build:
	# create directories
	sudo rm -rf www/ && mkdir www/; \
	sudo rm -rf mysql/data/ && mkdir mysql/data;
	# extract
	cd ./www/; \
	find ../data/ -name "Magento-CE-*.tar.gz" | head -n 1 | xargs tar zxf; \
	chmod -R 777 var/; \
	find bin/ -type f | xargs chmod +x
	# build images
	docker-compose build

composer:
	docker exec php sh -c 'curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer'
	docker exec php sh -c 'composer install -v'

magento_install:
	docker exec php sh /srv/assets/magento-install
	docker exec php php -dmemory_limit=1G bin/magento setup:static-content:deploy -l pl_PL
	docker exec php chmod -R 777 var/ pub/static/
	docker exec php php bin/magento deploy:mode:set developer
	docker exec php php bin/magento indexer:reindex

start:
	docker-compose up -d --remove-orphans

stop:
	docker-compose stop

restart: stop start

wait_3:
	sleep 3

wait_10:
	sleep 10

php_cli:
	docker exec -ti php bash

php_logs:
	docker logs -f php

varnish_cli:
	docker exec -ti docker_varnish_1 bash

nginx_cli:
	docker exec -ti docker_web_1 bash

nginx_logs:
	docker logs -f docker_web_1

mysql_cli:
	docker exec -ti docker_mysql_1 mysql -uroot -proot magento

magento_cli:
	echo $@
	docker exec php bin/magento

show_rebuild_warning:
	echo "Rebuilding in 5 seconds"
	echo "This will destroy ALL your data"
	#sleep 5