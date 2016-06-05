COMPOSER_FILE = test/environment/docker-compose.yml
PROJECT_NAME = dbmigrate



tests:
	docker-compose -f $(COMPOSER_FILE) -p $(PROJECT_NAME) up -d
	@while ! nc -z `docker-machine ip default` 3306; do sleep 0.1; done
	-DATABASE_HOST=`docker-machine ip default`:3306 php vendor/phpunit/phpunit/phpunit --coverage-html /tmp/dbmigrate-coverage
	-docker-compose -f $(COMPOSER_FILE) -p $(PROJECT_NAME) kill
	-docker-compose -f $(COMPOSER_FILE) -p $(PROJECT_NAME) rm -f
