pwd= $(shell pwd)
php_tester_version= "0.1.0"


.PHONY: prod-dependencies
prod-dependencies:
	docker pull php:7.2-apache

.PHONY: deploy
deploy: prod-dependencies
	docker run -d --name acme-project -v $(pwd)/src:/var/www/html -p 30001:80 php:7.2-apache

.PHONY: undeploy
undeploy:
	@-docker stop acme-project
	@-docker rm -f acme-project

.PHONY: clean
clean: undeploy
	@-docker rmi -f php:7.2-apache
	@-rm .test-php-docker-meta .test-js-docker-meta

.test-php-docker-meta: $(pwd)/config/composer.json
	docker build -t php_tester:v$(php_tester_version) -f Dockerfile.test.php .
	touch $@

.test-js-docker-meta: $(pwd)/config/karma.conf.js
	docker pull hochzehn/karma-jasmine-phantomjs
	touch $@

.PHONY: test
test: .test-php-docker-meta .test-js-docker-meta
	docker run --name php-tester -v $(pwd)/src/php:/app -v $(pwd)/test/php:/tests -it --rm php_tester:v$(php_tester_version)
	docker run --name js-tester -v $(pwd)/config/karma.conf.js:/opt/karma/karma.conf.js -v $(pwd)/src:/opt/karma/src -v $(pwd)/test/js:/opt/karma/tests -it --rm hochzehn/karma-jasmine-phantomjs


.PHONY: stop-test
stop-test:
	@-docker stop php-tester
	@-docker rm -f php-tester
	@-docker stop js-tester
	@-docker rm -f js-tester

.PHONY: clean-test
clean-test: stop-test
	@-docker rmi -f php_tester:v$(php_tester_version)
	@-docker rmi -f hocjzehn/karma-jasmine-phantomjs
	@-rm .test-php-docker-meta .test-js-build-meta
