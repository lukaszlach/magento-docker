VERSION ?= 1.9

default: restart

rebuild: show_rebuild_warning stop build-$(VERSION) start wait_15 install-$(VERSION) start

build-2.1:
	export MAGENTO_VERSION=2.1
	export MAGENTO_VERSION_SHORT=2
	# create directories
	sudo rm -rf www/ && mkdir www/
	sudo rm -rf mysql/data/ && mkdir -p mysql/data
	sudo rm -rf sphinxsearch/data/ && mkdir -p sphinxsearch/data
	# extract
	cd ./www/; \
	find ../install/ -name "Magento-CE-*.tar.gz" -name "*$(VERSION)*" | head -n 1 | xargs tar zxf
	cd ./www/; \
	chmod -R 777 var/ app/etc pub/media pub/static; \
	find bin/ -type f | xargs chmod +x
	# container configuration
	cp nginx/config/2.1/nginx-web.conf nginx/config/nginx-web.conf
	# build images
	docker-compose build

install-2.1:
	make composer
	docker-compose run php_cli sh /srv/assets/magento2-install

build-1.9:
	export MAGENTO_VERSION=1.9
	export MAGENTO_VERSION_SHORT=1
	# create directories
	sudo rm -rf www/ && mkdir www/
	sudo rm -rf mysql/data/ && mkdir mysql/data
	sudo rm -rf sphinxsearch/data/ && mkdir sphinxsearch/data
	# extract
	cd ./www/; \
	find ../install/ -name "*.tar.gz" -name "*$(VERSION)*" | head -n 1 | xargs tar zxf
	cd ./www/; \
	chmod -R 777 var/ app/etc
	# container configuration
	cp nginx/config/1.9/nginx-web.conf nginx/config/nginx-web.conf
	# build images
	docker-compose build

install-1.9:
	docker exec -u root php bash -c "echo '$$(docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' nginx) magento.local' >> /etc/hosts"
	docker exec -u www-data php sh /srv/assets/magento1-install
	docker exec -u root nginx chown -R www-data:www-data /srv/www
	docker exec -u root php n98-magerun sys:info

composer:
	docker exec php composer install -v

start:
	docker-compose up -d --remove-orphans

stop:
	docker-compose stop

restart: stop start

wait_15:
	sleep 15

php_cli:
	docker exec -ti php bash

php_logs:
	docker logs -f php

varnish_cli:
	docker exec -ti varnish bash

nginx_cli:
	docker exec -ti nginx bash

nginx_logs:
	docker logs -f nginx

mysql_cmd:
	docker exec -ti mysql mysql -uroot -proot magento

sphinx_cli:
	docker exec -ti sphinx bash

sphinx_cmd:
	docker exec -ti mysql mysql -hsphinx -P9306 -uroot -proot

sphinx_logs:
	docker logs -f sphinx

show_rebuild_warning:
	echo "Rebuilding in 5 seconds"
	echo "This will destroy ALL your data"
	sleep 5